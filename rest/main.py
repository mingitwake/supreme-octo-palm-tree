from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from chromawrapper import create, read, write, clean, show, delete
from document_loader import read_urls, read_files

app = FastAPI()

origins = [
    "http://localhost:8000",
    "http://localhost:8080"
]

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Define Pydantic models for request bodies
class CreateRequest(BaseModel):
    collection: str

class WriteUrlRequest(BaseModel):
    url: str
    id: str
    collection: str

class WriteFileRequest(BaseModel):
    file: str
    id: str
    collection: str

class DeleteRequest(BaseModel):
    id: str
    collection: str

class ShowRequest(BaseModel):
    collection: str

class ChatRequest(BaseModel):
    collection: str
    query: str
    histories: str

class CleanRequest(BaseModel):
    collection: str

@app.post("/chat")
async def chat(request: ChatRequest):
    try:
        result = read(collection_name=request.collection, query=request.query, histories=request.histories)
        return {"message": result}
    except Exception:
        raise HTTPException(status_code=500, detail="Error")

@app.post("/create")
async def create_collection(request: CreateRequest):
    try:
        create(collection_name=request.collection)
        return {"message": "OK"}
    except Exception:
        raise HTTPException(status_code=500, detail="Error")

@app.put("/upload_url")
async def write_document(request: WriteUrlRequest):
    try:
        document_list = read_urls(urls=[request.url], ids=[request.id])
        write(collection_name=request.collection, documents=document_list)
        return {"message": f"Document #{request.id} Uploaded"}
    except Exception:
        raise HTTPException(status_code=500, detail="Error")
    
@app.put("/upload_file")
async def write_document(request: WriteFileRequest):
    try:
        document_list = read_files(files=[request.file], ids=[request.id])
        write(collection_name=request.collection, documents=document_list)
        return {"message": f"Document #{request.id} Uploaded"}
    except Exception:
        raise HTTPException(status_code=500, detail="Error")

@app.delete("/clean")
async def clean_collection(request: CleanRequest):
    try:
        result = clean(collection_name=request.collection)
        return {"message": result}
    except Exception:
        raise HTTPException(status_code=500, detail="Error")
    
@app.post("/show")
async def show_documents(request: ShowRequest):
    try:
        result=show(collection_name=request.collection)
        return {"message": result}
    except Exception:
        raise HTTPException(status_code=500, detail="Error")
    
@app.delete("/delete")
async def delete_document(request: DeleteRequest):
    try:
        delete(collection_name=request.collection,id=request.id)
        return {"message": "OK"}
    except Exception:
        raise HTTPException(status_code=500, detail="Error")

# Run the app with `uvicorn main:app --port 5000 --reload`