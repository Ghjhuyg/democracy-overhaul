import asyncio
from contextlib import asynccontextmanager
from typing import List
import json

from fastapi import FastAPI, WebSocket, WebSocketDisconnect

from .routers import bills
from .auth import router as auth_router
from .redis_client import get_redis

# ---------- WebSocket менеджер ----------
class ConnectionManager:
    def __init__(self):
        self.active_connections: List[WebSocket] = []

    async def connect(self, websocket: WebSocket):
        await websocket.accept()
        self.active_connections.append(websocket)

    def disconnect(self, websocket: WebSocket):
        if websocket in self.active_connections:
            self.active_connections.remove(websocket)

    async def broadcast(self, message: str):
        for connection in self.active_connections:
            try:
                await connection.send_text(message)
            except Exception:
                pass

manager = ConnectionManager()

# ---------- Фоновая задача: подписка на Redis ----------
count = 0
async def redis_subscriber():
    global count
    redis = await get_redis()
    pubsub = redis.pubsub()
    await pubsub.subscribe("vote_events")
    while True:
        try:
            message = await pubsub.get_message(ignore_subscribe_messages=True)
            if message:
                count += 1
                print(f"Received from Redis #{count}: {message['data']}")
                data = json.loads(message['data'])
                event_type = data.get('type')
                
                if event_type == 'vote':
                    bill_id = data.get('bill_id')
                    await manager.broadcast(json.dumps(data))
                else:
                    await manager.broadcast(json.dumps(data))
        except Exception as e:
            print(f"Redis subscriber error: {e}")
            await asyncio.sleep(1)

# ---------- Lifespan ----------
@asynccontextmanager
async def lifespan(app: FastAPI):
    task = asyncio.create_task(redis_subscriber())
    yield
    task.cancel()

# ---------- Создание приложения ----------
app = FastAPI(title="Democracy Overhaul API", version="1.0", lifespan=lifespan)

app.include_router(bills.router)
app.include_router(auth_router)

# ---------- WebSocket эндпоинт ----------
@app.websocket("/ws")
async def websocket_endpoint(websocket: WebSocket):
    await manager.connect(websocket)
    try:
        while True:
            await websocket.receive_text()  # keep-alive
    except WebSocketDisconnect:
        manager.disconnect(websocket)

# ---------- Запуск (для отладки) ----------
if __name__ == "__main__":
    import uvicorn
    uvicorn.run("main:app", host="0.0.0.0", port=8000, reload=True)