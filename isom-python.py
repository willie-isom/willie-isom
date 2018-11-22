from flask import Flask, request, abort

from linebot import (
    LineBotApi, WebhookHandler
)
from linebot.exceptions import (
    InvalidSignatureError
)
from linebot.models import (
    MessageEvent, TextMessage, TextSendMessage,
)

app = Flask(__name__)

line_bot_api = LineBotApi('e4c6b9b5c1de15d032a23567dae6b2ee')
handler = WebhookHandler('8sQRF63aQIMd04MKWiJ77uY0E4u7UohzSu+cAS8g3ZDZHApiGcTrnoyzzPRtaXjRfqAe4U6KKC1RvEhYDXN1vTh7H9RFwNCtdo78u52aOwZsZRr6CyYAEcWrjMgLoqXJrLz7PGBqC7Pkhf/afx+C/AdB04t89/1O/w1cDnyilFU=')


@app.route("/callback", methods=['POST'])
def callback():
    # get X-Line-Signature header value
    signature = request.headers['X-Line-Signature']

    # get request body as text
    body = request.get_data(as_text=True)
    app.logger.info("Request body: " + body)

    # handle webhook body
    try:
        handler.handle(body, signature)
    except InvalidSignatureError:
        abort(400)

    return 'OK'


@handler.add(MessageEvent, message=TextMessage)
def handle_message(event):
    line_bot_api.reply_message(
        event.reply_token,
        TextSendMessage(text=event.message.text))


if __name__ == "__main__":
    app.run()
