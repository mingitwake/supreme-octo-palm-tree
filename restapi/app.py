# references: https://www.geeksforgeeks.org/python-build-a-rest-api-using-flask/

from flask import Flask, jsonify, request 
from flask_restful import Resource, Api 
from chromawrapper import read, write, clean
from document_loader import read_urls

app = Flask(__name__) 
api = Api(app)

class Upload(Resource): 
    
    def post(self):
        data = request.json
        # sysname = data.get('sysname')
        document = data.get('document')
        if not document:
            return jsonify({'error': 'No document provided'}), 400
        collection = data.get('collection')
        if not collection:
            collection = "default"
        try:
            documentList = read_urls(urls=[document])
            result = write(collection_name=collection, documents=documentList)
            return jsonify({'message': result})
        except Exception:
            return jsonify({'error': 1}), 500
    
class Chat(Resource):

    def post(self):
        data = request.json
        query = data.get('query')
        # sysname = data.get('sysname')
        if not query:
            return jsonify({'error': 'No query provided'}), 400
        collection = data.get('collection')
        if not collection:
            collection = "default"
        try:
            result = read(collection_name=collection, query=query)
            return jsonify({'message': result})
        except Exception:
            return jsonify({'error': 1}), 500
    
class Clean(Resource): 

    def post(self): 
        data = request.json
        collection = data.get('collection')
        if not collection:
            collection = "default"
        try:
            result = clean(collection_name=collection)
            return jsonify({'message': result})
        except Exception:
            return jsonify({'error': 1}), 500

# adding the defined resources along with their corresponding urls 
api.add_resource(Upload, '/upload') 
api.add_resource(Chat, '/chat') 
api.add_resource(Clean, '/clean') 
# driver function 
if __name__ == '__main__': 
    app.run(debug = True) 