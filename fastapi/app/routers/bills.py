from fastapi import APIRouter, Depends, HTTPException, Query
from sqlalchemy.ext.asyncio import AsyncSession
from sqlalchemy import select, func
from typing import List, Optional

from ..database import get_db
from ..models import Bill, User, Vote
from ..schemas import BillListSchema, BillDetailSchema, VoteSchema

router = APIRouter(prefix="/bills", tags=["bills"])

@router.get("/", response_model=List[BillListSchema])
async def get_bills(
    status: Optional[str] = None,
    search: Optional[str] = None,
    limit: int = Query(10, ge=1, le=100),
    offset: int = Query(0, ge=0),
    db: AsyncSession = Depends(get_db)
):
    query = select(
        Bill.id,
        Bill.title,
        Bill.status,
        Bill.voting_start_at,
        Bill.voting_end_at,
        User.name.label("user_name"),
        func.sum(func.if_(Vote.vote == "for", 1, 0)).label("votes_for"),
        func.sum(func.if_(Vote.vote == "against", 1, 0)).label("votes_against"),
    ).join(User, User.id == Bill.user_id)\
     .outerjoin(Vote, Vote.bill_id == Bill.id)\
     .group_by(Bill.id, User.name)

    if status:
        query = query.where(Bill.status == status)
    if search:
        query = query.where(Bill.title.like(f"%{search}%"))

    query = query.offset(offset).limit(limit)
    result = await db.execute(query)
    rows = result.all()

    return [
        BillListSchema(
            id=row.id,
            title=row.title,
            status=row.status,
            voting_start_at=row.voting_start_at,
            voting_end_at=row.voting_end_at,
            user_name=row.user_name,
            votes_for=row.votes_for or 0,
            votes_against=row.votes_against or 0,
        )
        for row in rows
    ]

@router.get("/{bill_id}", response_model=BillDetailSchema)
async def get_bill_detail(bill_id: int, db: AsyncSession = Depends(get_db)):
    bill_query = select(
        Bill.id,
        Bill.title,
        Bill.description,
        Bill.status,
        Bill.voting_start_at,
        Bill.voting_end_at,
        User.name.label("user_name"),
    ).join(User, User.id == Bill.user_id).where(Bill.id == bill_id)
    bill_result = await db.execute(bill_query)
    bill_row = bill_result.first()
    if not bill_row:
        raise HTTPException(status_code=404, detail="Bill not found")

    votes_query = select(
        Vote.user_id,
        User.name.label("user_name"),
        Vote.vote,
        Vote.created_at
    ).join(User, User.id == Vote.user_id).where(Vote.bill_id == bill_id)
    votes_result = await db.execute(votes_query)
    votes = [
        VoteSchema(
            user_id=row.user_id,
            user_name=row.user_name,
            vote=row.vote,
            created_at=row.created_at,
        )
        for row in votes_result.all()
    ]

    votes_for = sum(1 for v in votes if v.vote == "for")
    votes_against = sum(1 for v in votes if v.vote == "against")

    return BillDetailSchema(
        id=bill_row.id,
        title=bill_row.title,
        description=bill_row.description,
        status=bill_row.status,
        voting_start_at=bill_row.voting_start_at,
        voting_end_at=bill_row.voting_end_at,
        user_name=bill_row.user_name,
        votes_for=votes_for,
        votes_against=votes_against,
        votes=votes,
    )