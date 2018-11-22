from flask import Flask, request, abort

from linebot import (LineBotApi, WebhookHandler)
from linebot.exceptions import (InvalidSignatureError)
from linebot.models import (MessageEvent, TextMessage, TextSendMessage)

app = Flask(__name__)

line_bot_api = LineBotApi('YxTSr5T6yB1vNRmZNXGo5tqBMwgelhxFvjHdG9Ymi+fHREqFOtKpVeNEtYrX2I4uEd0z5rCpJuL3ei8hCXF9xCWzrg3Bw7oPzf4D+QzqJKazGEAIs9u7N50v1odKWUlNWtGCa9twFhm0rshrm6bqTAdB04t89/1O/w1cDnyilFU=')
handler = WebhookHandler('3c698809cb17ebbe581aebe712c76baf')


@app.route("", methods=['POST'])
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
