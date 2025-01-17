from flask import Flask, request, jsonify
import pickle
import psycopg2
from psycopg2.extras import RealDictCursor
import numpy as np
import pandas as pd
from dateutil.parser import parse as parse_date
from sklearn.preprocessing import StandardScaler
from sklearn.linear_model import LinearRegression
from elmz import elm
from fwi import FWICLASS
from datetime import datetime
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

def format_date(date):
    return date.strftime('%Y-%m-%d')

class CustomUnpickler(pickle.Unpickler):
    def find_class(self, module, name):
        if module == "__main__":
            module = "elmz"
        return super().find_class(module, name)

with open('/home/forestfirepredictionelm/ffp-api/modelV2.pkl', 'rb') as file:
    model = CustomUnpickler(file).load()

connection = psycopg2.connect(user="ffp-indonesia",
                              password="forestfire123",
                              host="34.101.120.221",
                              port="5432",
                              database="ffp-indonesia",
                              cursor_factory=RealDictCursor)

def get_db_connection():
    return psycopg2.connect(user="ffp-indonesia",
                            password="forestfire123",
                            host="34.101.120.221",
                            port="5432",
                            database="ffp-indonesia",
                            cursor_factory=RealDictCursor)

# Define global scaler and dataframe
scaler_X = StandardScaler()
scaler_y = StandardScaler()
df = pd.DataFrame()

def fetch_data(selected_provinsi, selected_kabupaten):
    cursor = connection.cursor()
    query = "SELECT provinsi, date, windspeed, humidity, rainfall, temperature FROM posts WHERE kabupaten = %s AND provinsi = %s"
    cursor.execute(query, (selected_kabupaten, selected_provinsi))
    data = cursor.fetchall()
    cursor.close()
    return pd.DataFrame(data)
    
@app.route('/predict/api/getkota', methods=['GET','POST'])
def getkota():
    data = request.json
    id_provinsi = data.get('id_provinsi')
    id_provinsi = str(id_provinsi)
    if not id_provinsi:
        return jsonify({'error': 'Province ID is required'}), 400

    connection = get_db_connection()
    cursor = connection.cursor()
    try:
        query = "SELECT id, name FROM regencies WHERE province_id = %s"
        cursor.execute(query, (id_provinsi,))
        regencies = cursor.fetchall()

        if not regencies:
            return jsonify({'error': 'No regencies found for the provided province ID'}), 404

        regency_list = [{'id': reg['id'], 'name': reg['name']} for reg in regencies]
        return jsonify(regency_list)
    except Exception as e:
        return jsonify({'error': str(e)}), 500
    finally:
        cursor.close()
        connection.close()

@app.route('/predict/train/temperature', methods=['GET'])
def train_temp():
    global df, scaler_X, scaler_y, model
    selected_provinsi = request.args.get('provinsi')
    selected_kabupaten = request.args.get('kabupaten')
    df = fetch_data(selected_provinsi, selected_kabupaten)
    if df is None:
        return jsonify({"error": "No data found"}), 404

    df['date'] = pd.to_datetime(df['date'], format='%Y-%m-%d')
    train_data = df[df['date'] <= '2022-02-28']
    test_data = df[df['date'] > '2022-02-28']

    X_train = train_data[['humidity', 'windspeed', 'rainfall']].values
    y_train = train_data[['temperature']].values
    X_test = test_data[['humidity', 'windspeed', 'rainfall']].values
    y_test = test_data[['temperature']].values

    dates = test_data['date'].tolist()

    scaler_X = StandardScaler()
    scaler_y = StandardScaler()

    X_train_scaled = scaler_X.fit_transform(X_train)
    X_test_scaled = scaler_X.transform(X_test)

    y_train_scaled = scaler_y.fit_transform(y_train)
    y_test_scaled = scaler_y.transform(y_test)

    model = elm(hidden_units=40, activation_function='tanh', x=X_train_scaled, y=y_train_scaled, random_type='normal', C=1.0)
    model.fit()

    y_pred_scaled = model.predict(X_test_scaled)
    y_pred = scaler_y.inverse_transform(y_pred_scaled).round(1)
    y_test = scaler_y.inverse_transform(y_test_scaled).round(1)

    result_df = pd.DataFrame({
        'date': dates,
        'Actual Temperature': y_test.flatten(),
        'Predicted Temperature': y_pred.flatten()
    })

    def score(y_true, y_pred):
        mae = np.mean(np.abs(y_pred - y_true))
        mse = np.mean((y_pred - y_true) ** 2)
        r_squared = 1 - (np.sum((y_true - y_pred) ** 2) / np.sum((y_true - np.mean(y_true)) ** 2))
        return mae, mse, r_squared

    def modified_mape_temp(y_true, y_pred):
        y_true_mod = y_true
        return np.mean(np.abs((y_true - y_pred) / y_true_mod)) * 100

    y_test_flat = y_test.flatten()
    y_pred_flat = y_pred.flatten()

    mae, mse, r_squared = score(y_test_flat, y_pred_flat)
    mape = modified_mape_temp(y_test_flat, y_pred_flat)

    metrics = {
        'MAE': mae,
        'MSE': mse,
        'MAPE': mape,
        'R-Squared': r_squared
    }

    response = {
        'result': result_df.to_dict(orient='records'),
        'metrics': metrics
    }

    return jsonify(response)


@app.route('/predict/train/humidity', methods=['GET'])
def train_humid():
    global df, scaler_X, scaler_y, model
    selected_provinsi = request.args.get('provinsi')
    selected_kabupaten = request.args.get('kabupaten')
    df = fetch_data(selected_provinsi, selected_kabupaten)
    if df is None:
        return jsonify({"error": "No data found"}), 404

    df['date'] = pd.to_datetime(df['date'], format='%Y-%m-%d')
    train_data = df[df['date'] <= '2022-02-28']
    test_data = df[df['date'] > '2022-02-28']

    X_train = train_data[['temperature', 'windspeed', 'rainfall']].values
    y_train = train_data[['humidity']].values
    X_test = test_data[['temperature', 'windspeed', 'rainfall']].values
    y_test = test_data[['humidity']].values

    dates = test_data['date'].tolist()

    scaler_X = StandardScaler()
    scaler_y = StandardScaler()

    X_train_scaled = scaler_X.fit_transform(X_train)
    X_test_scaled = scaler_X.transform(X_test)

    y_train_scaled = scaler_y.fit_transform(y_train)
    y_test_scaled = scaler_y.transform(y_test)

    model = elm(hidden_units=60, activation_function='leaky_relu', x=X_train_scaled, y=y_train_scaled, random_type='he', C=1.0)
    model.fit()

    y_pred_scaled = model.predict(X_test_scaled)
    y_pred = scaler_y.inverse_transform(y_pred_scaled).round(1)
    y_test = scaler_y.inverse_transform(y_test_scaled).round(1)

    result_df = pd.DataFrame({
        'date': dates,
        'Actual Humidity': y_test.flatten(),
        'Predicted Humidity': y_pred.flatten()
    })

    def score(y_true, y_pred):
        mae = np.mean(np.abs(y_pred - y_true))
        mse = np.mean((y_pred - y_true) ** 2)
        r_squared = 1 - (np.sum((y_true - y_pred) ** 2) / np.sum((y_true - np.mean(y_true)) ** 2))
        return mae, mse, r_squared

    def modified_mape_humid(y_true, y_pred):
        y_true_mod = y_true
        return np.mean(np.abs((y_true - y_pred) / y_true_mod)) * 100

    y_test_flat = y_test.flatten()
    y_pred_flat = y_pred.flatten()

    mae, mse, r_squared = score(y_test_flat, y_pred_flat)
    mape = modified_mape_humid(y_test_flat, y_pred_flat)

    metrics = {
        'MAE': mae,
        'MSE': mse,
        'MAPE': mape,
        'R-Squared': r_squared
    }

    response = {
        'result': result_df.to_dict(orient='records'),
        'metrics': metrics
    }

    return jsonify(response)

@app.route('/predict/train/rainfall', methods=['GET'])
def train_rain():
    global df, scaler_X, scaler_y, model
    selected_provinsi = request.args.get('provinsi')
    selected_kabupaten = request.args.get('kabupaten')
    df = fetch_data(selected_provinsi, selected_kabupaten)
    if df.empty:
        return jsonify({"error": "No data found"}), 404

    df['date'] = pd.to_datetime(df['date'], format='%Y-%m-%d')

    df['Rainfall_adjusted'] = df['rainfall'].replace(0, 1)
    df['Rainfall_log'] = np.log1p(df['Rainfall_adjusted'])

    train_data = df[df['date'] <= '2022-02-28']
    test_data = df[df['date'] > '2022-02-28']

    X_train = train_data[['humidity', 'rainfall', 'windspeed']].values
    y_train = train_data['Rainfall_log'].values.reshape(-1, 1)
    X_test = test_data[['humidity', 'rainfall', 'windspeed']].values
    y_test = test_data['Rainfall_log'].values.reshape(-1, 1)

    dates = test_data['date'].tolist()

    scaler_X = StandardScaler()
    scaler_y = StandardScaler()

    X_train_scaled = scaler_X.fit_transform(X_train)
    X_test_scaled = scaler_X.transform(X_test)

    y_train_scaled = scaler_y.fit_transform(y_train)
    y_test_scaled = scaler_y.transform(y_test)

    model = elm(hidden_units=60, activation_function='relu', x=X_train_scaled, y=y_train_scaled, random_type='normal', C=1.0)
    model.fit()

    y_pred_scaled = model.predict(X_test_scaled)
  
    y_pred_denorm =  np.expm1(scaler_y.inverse_transform(y_pred_scaled)).round(1)
    y_test_denorm =  np.expm1(scaler_y.inverse_transform(y_test_scaled)).round(1)

    result_df = pd.DataFrame({
        'date': dates,
        'Actual Rainfall': y_test_denorm.flatten(),
        'Predicted Rainfall': y_pred_denorm.flatten()
    })

    def score(y_true, y_pred):
        mae = np.mean(np.abs(y_pred - y_true))
        mse = np.mean((y_pred - y_true) ** 2)
        r_squared = 1 - (np.sum((y_true - y_pred) ** 2) / np.sum((y_true - np.mean(y_true)) ** 2))
        return mae, mse, r_squared

    def modified_mape_rain(y_true, y_pred, c=0.0001):
        y_true_mod = y_true + c
        return np.mean(np.abs((y_true - y_pred) / y_true_mod)) * 100

    y_test_denorm_flat = y_test_denorm.flatten()
    y_pred_denorm_flat = y_pred_denorm.flatten()

    mae, mse, r_squared = score(y_test_denorm_flat, y_pred_denorm_flat)
    mape = modified_mape_rain(y_test_denorm_flat, y_pred_denorm_flat)

    metrics = {
        'MAE': mae,
        'MSE': mse,
        'MAPE': mape,
        'R-Squared': r_squared
    }

    response = {
        'result': result_df.to_dict(orient='records'),
        'metrics': metrics
    }

    return jsonify(response)

@app.route('/predict/train/windspeed', methods=['GET'])
def train_wind():
    global df, scaler_X, scaler_y, model
    selected_provinsi = request.args.get('provinsi')
    selected_kabupaten = request.args.get('kabupaten')
    df = fetch_data(selected_provinsi, selected_kabupaten)
    if df is None:
        return jsonify({"error": "No data found"}), 404

    df['date'] = pd.to_datetime(df['date'], format='%Y-%m-%d')
    train_data = df[df['date'] <= '2022-02-28']
    test_data = df[df['date'] > '2022-02-28']

    X_train = train_data[['temperature', 'humidity', 'rainfall']].values
    y_train = train_data[['windspeed']].values
    X_test = test_data[['temperature', 'humidity', 'rainfall']].values
    y_test = test_data[['windspeed']].values

    dates = test_data['date'].tolist()

    scaler_X = StandardScaler()
    scaler_y = StandardScaler()

    X_train_scaled = scaler_X.fit_transform(X_train)
    X_test_scaled = scaler_X.transform(X_test)

    y_train_scaled = scaler_y.fit_transform(y_train)
    y_test_scaled = scaler_y.transform(y_test)

    model = elm(hidden_units=80, activation_function='sigmoid', x=X_train_scaled, y=y_train_scaled, random_type='he', C=0.001)
    model.fit()

    y_pred_scaled = model.predict(X_test_scaled)
    y_pred = scaler_y.inverse_transform(y_pred_scaled).round(1)
    y_test = scaler_y.inverse_transform(y_test_scaled).round(1)

    result_df = pd.DataFrame({
        'date': dates,
        'Actual Windspeed': y_test.flatten(),
        'Predicted Windspeed': y_pred.flatten()
    })

    def score(y_true, y_pred):
        mae = np.mean(np.abs(y_pred - y_true))
        mse = np.mean((y_pred - y_true) ** 2)
        r_squared = 1 - (np.sum((y_true - y_pred) ** 2) / np.sum((y_true - np.mean(y_true)) ** 2))
        return mae, mse, r_squared

    def modified_mape_wind(y_true, y_pred, c=0.0001):
        y_true_mod = y_true + c
        return np.mean(np.abs((y_true - y_pred) / y_true_mod)) * 100

    y_test_flat = y_test.flatten()
    y_pred_flat = y_pred.flatten()

    mae, mse, r_squared = score(y_test_flat, y_pred_flat)
    mape = modified_mape_wind(y_test_flat, y_pred_flat)

    metrics = {
        'MAE': mae,
        'MSE': mse,
        'MAPE': mape,
        'R-Squared': r_squared
    }

    response = {
        'result': result_df.to_dict(orient='records'),
        'metrics': metrics
    }

    return jsonify(response)

@app.route('/predict/train', methods=['POST', 'GET'])
def final_data():
    global df, scaler_X, scaler_y, model
    data = request.json
    selected_provinsi = data.get('selectedProvinsi')
    selected_kabupaten = data.get('selectedKabupaten')

    response_temp = app.test_client().get('/predict/train/temperature', query_string={'provinsi': selected_provinsi, 'kabupaten': selected_kabupaten})
    response_humid = app.test_client().get('/predict/train/humidity', query_string={'provinsi': selected_provinsi, 'kabupaten': selected_kabupaten})
    response_rain = app.test_client().get('/predict/train/rainfall', query_string={'provinsi': selected_provinsi, 'kabupaten': selected_kabupaten})
    response_wind = app.test_client().get('/predict/train/windspeed', query_string={'provinsi': selected_provinsi, 'kabupaten': selected_kabupaten})

    result_temp = response_temp.get_json().get('result', [])
    metrics_temp = response_temp.get_json().get('metrics', {})
    result_humid = response_humid.get_json().get('result', [])
    metrics_humid = response_humid.get_json().get('metrics', {})
    result_rain = response_rain.get_json().get('result', [])
    metrics_rain = response_rain.get_json().get('metrics', {})
    result_wind = response_wind.get_json().get('result', [])
    metrics_wind = response_wind.get_json().get('metrics', {})

    ffmc0 = 85.0  
    dmc0 = 6.0    
    dc0 = 15.0

    predict_data = []
    for temp, humid, rain, wind in zip(result_temp, result_humid, result_rain, result_wind):
        date_str = temp['date']
        date = parse_date(date_str)
        formatted_date = format_date(date)
        month = date.month

        cursor = connection.cursor()
        
        delete_query = """
        DELETE FROM post_predicts WHERE provinsi = %s AND kabupaten = %s AND date = %s
        """
        cursor.execute(delete_query, (selected_provinsi, selected_kabupaten, formatted_date))

        delete_query_fwi = """
        DELETE FROM fwis WHERE provinsi = %s AND kabupaten = %s AND date = %s
        """
        cursor.execute(delete_query_fwi, (selected_provinsi, selected_kabupaten, formatted_date))

        insert_query = """
        INSERT INTO post_predicts (provinsi, kabupaten, temperature_predict, rainfall_predict, humidity_predict, windspeed_predict, date)
        VALUES (%s, %s, %s, %s, %s, %s, %s)
        """
        cursor.execute(insert_query, (
            selected_provinsi, selected_kabupaten, temp['Predicted Temperature'], rain['Predicted Rainfall'], humid['Predicted Humidity'], wind['Predicted Windspeed'], formatted_date
        ))

        fwi_instance = FWICLASS(temp=temp['Actual Temperature'], rhum=humid['Actual Humidity'], wind=wind['Actual Windspeed'], prcp=rain['Actual Rainfall'])
        
        ffmc = fwi_instance.FFMCcalc(ffmc0)
        dmc = fwi_instance.DMCcalc(dmc0, month)
        dc = fwi_instance.DCcalc(dc0, month)
        isi = fwi_instance.ISIcalc(ffmc)
        bui = fwi_instance.BUIcalc(dmc, dc)
        fwi = fwi_instance.FWIcalc(isi, bui)

        insert_query_fwi = """
        INSERT INTO fwis (provinsi, kabupaten, ffmc, dmc, dc, isi, bui, fwi, date)
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)
        """
        cursor.execute(insert_query_fwi, (
            selected_provinsi, selected_kabupaten, ffmc, dmc, dc, isi, bui, fwi, formatted_date
        ))

        connection.commit()
        cursor.close()

        predict_data.append({
            'Date': formatted_date,
            'Predicted Temperature': temp['Predicted Temperature'],
            'Predicted Humidity': humid['Predicted Humidity'],
            'Predicted Rainfall': rain['Predicted Rainfall'],
            'Predicted Windspeed': wind['Predicted Windspeed'],
            'FFMC': ffmc,
            'DMC': dmc,
            'DC': dc,
            'ISI': isi,
            'BUI': bui,
            'FWI': fwi
        })

        ffmc0 = ffmc
        dmc0 = dmc
        dc0 = dc

    response = {
        'predicted_data': predict_data,
        'metrics': {
            'temperature': metrics_temp,
            'humidity': metrics_humid,
            'rainfall': metrics_rain,
            'windspeed': metrics_wind
        }
    }

    return jsonify(response)



@app.route('/predict/api/fwi-data-map', methods=['GET', 'POST'])
def fwi_data():
    data = request.json
    date = data['date']
    
    cursor = connection.cursor()
    q = ("""
            SELECT date, temperature_predict, humidity_predict, rainfall_predict, windspeed_predict, provinsi, kabupaten
            FROM post_predicts
            WHERE date = %s
            """)
    cursor.execute(q, (date,))
    data = cursor.fetchall()

    results = []

    ffmc0 = 85.0  
    dmc0 = 6.0    
    dc0 = 15.0

    for entry in data:
        kabupaten = entry['kabupaten']
        provinsi = entry['provinsi']
        date = entry['date']
        date_out = format_date(date)
        windspeed = entry['windspeed']
        humidity = entry['humidity']
        rainfall = entry['rainfall']
        temperature = entry['temperature']
        
        cursor = connection.cursor()
        q1 = ("SELECT name FROM regencies WHERE id = %s AND province_id = %s")
        cursor.execute(q1, (kabupaten, provinsi))
        name_data = cursor.fetchall()

        for entry_name in name_data:
            name = entry_name['name']
        
        month = date.month
        
        fwi_instance = FWICLASS(temp=temperature, rhum=humidity, wind=windspeed, prcp=rainfall)
        
        ffmc = fwi_instance.FFMCcalc(ffmc0)
        dmc = fwi_instance.DMCcalc(dmc0, month)
        dc = fwi_instance.DCcalc(dc0, month)
        isi = fwi_instance.ISIcalc(ffmc)
        bui = fwi_instance.BUIcalc(dmc, dc)
        fwi = fwi_instance.FWIcalc(isi, bui)
        
        results.append({
            "kabupaten_id": kabupaten,
            "name": name,
            "date": date_out,
            "FFMC": ffmc,
            "DMC": dmc,
            "DC": dc,
            "ISI": isi,
            "BUI": bui,
            "FWI": fwi
        })

        ffmc0 = ffmc
        dmc0 = dmc
        dc0 = dc
    return jsonify(results)

@app.route('/predict/api/history', methods=['GET'])
def history():
    selected_provinsi = request.args.get('provinsi')
    selected_kabupaten = request.args.get('kabupaten')
    start_date = request.args.get('start_date')
    end_date = request.args.get('end_date')

    connection = get_db_connection()
    cursor = connection.cursor()

    query_posts = """
        SELECT date, temperature, humidity, rainfall, windspeed
        FROM posts
        WHERE provinsi = %s AND kabupaten = %s AND date BETWEEN %s AND %s
    """
    cursor.execute(query_posts, (selected_provinsi, selected_kabupaten, start_date, end_date))
    posts_data = cursor.fetchall()

    query_post_predict = """
        SELECT date, temperature_predict, humidity_predict, rainfall_predict, windspeed_predict
        FROM post_predicts
        WHERE provinsi = %s AND kabupaten = %s AND date BETWEEN %s AND %s
    """
    cursor.execute(query_post_predict, (selected_provinsi, selected_kabupaten, start_date, end_date))
    post_predict_data = cursor.fetchall()

    cursor.close()
    connection.close()

    if not posts_data and not post_predict_data:
        return jsonify({"error": "No data found"}), 404

    combined_data = {}
    
    for post in posts_data:
        date = post['date']
        combined_data[date] = {
            "date": date,
            "temperature": post['temperature'],
            "humidity": post['humidity'],
            "rainfall": post['rainfall'],
            "windspeed": post['windspeed'],
            "temperature_predict": None,
            "humidity_predict": None,
            "rainfall_predict": None,
            "windspeed_predict": None
        }

    for predict in post_predict_data:
        date = predict['date']
        if date in combined_data:
            combined_data[date]["temperature_predict"] = predict['temperature_predict']
            combined_data[date]["humidity_predict"] = predict['humidity_predict']
            combined_data[date]["rainfall_predict"] = predict['rainfall_predict']
            combined_data[date]["windspeed_predict"] = predict['windspeed_predict']
        else:
            combined_data[date] = {
                "date": date,
                "temperature": None,
                "humidity": None,
                "rainfall": None,
                "windspeed": None,
                "temperature_predict": predict['temperature_predict'],
                "humidity_predict": predict['humidity_predict'],
                "rainfall_predict": predict['rainfall_predict'],
                "windspeed_predict": predict['windspeed_predict']
            }

    combined_data_list = list(combined_data.values())

    return jsonify(combined_data_list)

@app.route('/predict/api/fwi-data-all', methods=['POST'])
def fwi_data_xx():
    data = request.json

    start_date = data['start_date']
    end_date = data['end_date']
    province = data['provinsi']
    kabupaten = data['kabupaten']
    
    connection = get_db_connection()
    cursor = connection.cursor(cursor_factory=RealDictCursor)
    q = """
        SELECT date, ffmc, dmc, dc, isi, bui, fwi, provinsi, kabupaten
        FROM fwis
        WHERE provinsi = %s AND kabupaten = %s AND date BETWEEN %s AND %s
    """
    cursor.execute(q, (province, kabupaten, start_date, end_date))
    data = cursor.fetchall()
    cursor.close()
    connection.close()

    if not data:
        return jsonify({"status": "error", "message": "No data found"}), 404

    results = []

    for entry in data:
        results.append({
            "date": entry['date'].strftime('%Y-%m-%d'),
            "FFMC": entry['ffmc'],
            "DMC": entry['dmc'],
            "DC": entry['dc'],
            "ISI": entry['isi'],
            "BUI": entry['bui'],
            "FWI": entry['fwi'],
            "provinsi": entry['provinsi'],
            "kabupaten": entry['kabupaten']
        })

    return jsonify({"status": "success", "data": results})

@app.route('/predict/api/fwi-data-current', methods=['POST'])
def fwi_data_current():
    data = request.json
    date = data.get('date', datetime.now().strftime('%Y-%m-%d'))

    connection = get_db_connection()
    cursor = connection.cursor()
    query = """
        SELECT f.date, f.ffmc, f.dmc, f.dc, f.isi, f.bui, f.fwi, f.provinsi, f.kabupaten, r.name
        FROM fwis f
        JOIN regencies r ON f.kabupaten = r.id AND f.provinsi = r.province_id
        WHERE f.date = %s
    """
    cursor.execute(query, (date,))
    data = cursor.fetchall()
    cursor.close()
    connection.close()

    if not data:
        return jsonify({"error": "No data found"}), 404

    results = []
    for entry in data:
        results.append({
            "kabupaten_id": entry['kabupaten'],
            "name": entry['name'],
            "date": entry['date'].strftime('%Y-%m-%d'),
            "FFMC": entry['ffmc'],
            "DMC": entry['dmc'],
            "DC": entry['dc'],
            "ISI": entry['isi'],
            "BUI": entry['bui'],
            "FWI": entry['fwi']
        })

    return jsonify(results)
    

if __name__ == '__main__':
    app.run(debug=True)
