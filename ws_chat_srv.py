#! /usr/bin/python3

import asyncio
import websockets
import threading
import ssl
import sys

client_list = []
client_list_lock = threading.Lock()

async def chat_server(websocket, path):
    print("client connected")
    with client_list_lock:
        client_list.append(websocket)
    while True:
        try:
            # try to receive a message
            msg = await websocket.recv()
            print("> %s" % msg)

            # broadcast out the message
            with client_list_lock:
                for client in client_list:
                    await client.send(msg)
        except:
            # disconnect, remove the client from the list
            print("client disconnected")
            with client_list_lock:
                client_list.remove(websocket)
            break

if len(sys.argv) < 3:
    print("usage: wschatsrv [cert_chain] [cert_key]")
    exit(1)

ssl_cert_chain = sys.argv[1]
ssl_cert_key = sys.argv[2]

ssl_context = ssl.SSLContext(ssl.PROTOCOL_TLS_SERVER)
ssl_cert = ssl_cert_chain
ssl_context.load_cert_chain(ssl_cert, keyfile=ssl_cert_key)
ws = websockets.serve(chat_server, "0.0.0.0", 7123, ssl=ssl_context)

print("starting websocket server")
asyncio.get_event_loop().run_until_complete(ws)
asyncio.get_event_loop().run_forever()
