# from chromawrapper import create, read, write, clear
from document_loader import read_urls
collection="admin"
# query=input()
# histories=input()
# result = read(collection, query, histories)
# print("{\"message\": "+result+"}")

print(read_urls(urls=["https://dictionary.cambridge.org/dictionary/english/happy"]))
