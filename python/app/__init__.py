import json
import os
from flask import Flask

from app.controllers import tirage


app = Flask(__name__)

app.register_blueprint(tirage.fifa)