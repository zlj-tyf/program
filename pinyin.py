from flask import Flask, request, jsonify
from pypinyin import pinyin, Style

app = Flask(__name__)

@app.route('/to_pinyin', methods=['POST'])
def to_pinyin():
    data = request.get_json()
    text = data.get('text', '')
    if not text:
        return jsonify({"error": "No text provided"}), 400

    # 转换为拼音
    py_list = pinyin(text, style=Style.NORMAL)
    py_flat = [item[0] for item in py_list]
    result = ''.join(py_flat)
    
    return jsonify({"pinyin": result})

if __name__ == '__main__':
    app.run(host='106.15.139.140', port=12344)

