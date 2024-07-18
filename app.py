from flask import Flask
from redis import Redis

server = Flask(__name__)
redis = Redis(host='redis', port=6379)

@server.route('/', methods=['GET', 'POST'])
def index():
    return "Hello World!"
        
if __name__ == "__main__":
    server.run(host="0.0.0.0", port=int("5000"), debug=True)  