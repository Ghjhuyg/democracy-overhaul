from pydantic import BaseModel
from typing import Optional, List
from datetime import datetime

class VoteSchema(BaseModel):
    user_id: int
    user_name: str
    vote: str
    created_at: datetime

    class Config:
        from_attributes = True

class BillListSchema(BaseModel):
    id: int
    title: str
    status: str
    voting_start_at: Optional[datetime]
    voting_end_at: Optional[datetime]
    user_name: str
    votes_for: int = 0
    votes_against: int = 0

    class Config:
        from_attributes = True

class BillDetailSchema(BillListSchema):
    description: str
    votes: List[VoteSchema] = []

class VoteRequest(BaseModel):
    user_id: int
    vote: str  # 'for' or 'against'