from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from chromawrapper import create, read, write, clear
from document_loader import read_urls

app = FastAPI()

origins = [
    "http://localhost:8000",
    "http://localhost:8080"
]

app.add_middleware(
    CORSMiddleware,
    allow_origins=origins,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Define Pydantic models for request bodies
class CreateRequest(BaseModel):
    collection: str

class WriteRequest(BaseModel):
    document: str
    collection: str

class ChatRequest(BaseModel):
    query: str
    collection: str

class CleanRequest(BaseModel):
    collection: str

@app.post("/chat")
async def chat(request: ChatRequest):
    try:
        result = read(collection_name=request.collection, query=request.query)
        return {"message": result}
    except Exception:
        raise HTTPException(status_code=500, detail="Error")

@app.post("/")
async def create_collection(request: CreateRequest):
    try:
        create(collection_name=request.collection)
        return {"message": "0"}
    except Exception:
        raise HTTPException(status_code=500, detail="Error")

@app.put("/")
async def write_document(request: WriteRequest):
    try:
        document_list = read_urls(urls=[request.document])
        write(collection_name=request.collection, documents=document_list)
        return {"message": "0"}
    except Exception:
        raise HTTPException(status_code=500, detail="Error")

@app.delete("/")
async def clear_collection(request: CleanRequest):
    try:
        result = clear(collection_name=request.collection)
        return {"message": result}
    except Exception:
        raise HTTPException(status_code=500, detail="Error")

# Run the app with `uvicorn main:app --port 5000 --reload`