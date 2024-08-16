from flask import Flask, jsonify
from flask_sqlalchemy import SQLAlchemy
import logging

app = Flask(__name__)

# Konfigurasi koneksi ke PostgreSQL
app.config['SQLALCHEMY_DATABASE_URI'] = 'postgresql://postgres:admin@127.0.0.1/postgres'

# Inisialisasi objek SQLAlchemy
db = SQLAlchemy(app)

# Inisialisasi logger
logging.basicConfig(level=logging.DEBUG)
logger = logging.getLogger(__name__)

# Model untuk tabel 'posts'
class Post(db.Model):
    __tablename__ = 'posts'
    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.Integer, db.ForeignKey('users.id'))
    provinsi = db.Column(db.Integer, db.ForeignKey('provinsis.id'))
    kabupaten = db.Column(db.Integer, db.ForeignKey('kabupatens.id'))
    temperature = db.Column(db.Float)
    rainfall = db.Column(db.Float)
    humidity = db.Column(db.Float)
    windspeed = db.Column(db.Float)
    date = db.Column(db.Date)
    published_at = db.Column(db.DateTime, nullable=True)
    created_at = db.Column(db.DateTime, default=db.func.current_timestamp())
    updated_at = db.Column(db.DateTime, default=db.func.current_timestamp(), onupdate=db.func.current_timestamp())

    def __repr__(self):
        return '<Post %r>' % self.id

# Route untuk menampilkan data dari tabel posts
@app.route('/')
def get_posts():
    try:
        posts = Post.query.all()
        post_list = []
        for post in posts:
            post_data = {
                'id': post.id,
                'user_id': post.user_id,
                'provinsi': post.provinsi,
                'kabupaten': post.kabupaten,
                'temperature': post.temperature,
                'rainfall': post.rainfall,
                'humidity': post.humidity,
                'windspeed': post.windspeed,
                'date': post.date.isoformat(),
                'published_at': post.published_at,
                'created_at': post.created_at.isoformat(),
                'updated_at': post.updated_at.isoformat()
            }
            post_list.append(post_data)
        return jsonify({'posts': post_list})
    except Exception as e:
        logger.error("Error fetching posts: %s", str(e))
        return jsonify({'error': 'Failed to fetch posts'}), 500

# Endpoint baru untuk query dari Python
@app.route('/query-from-python')
def query_from_python():
    try:
        # Lakukan query untuk mendapatkan data yang diinginkan
        # Misalnya:
        data = {'message': 'This is a sample response from Python Flask.'}
        return jsonify(data)
    except Exception as e:
        logger.error("Error querying data from Python: %s", str(e))
        return jsonify({'error': 'Failed to query data from Python'}), 500

# Jalankan aplikasi Flask
if __name__ == '__main__':
    app.run(port=8000, debug=True)
