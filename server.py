#! /usr/bin/python3

import asyncio
import websockets
import threading

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

ws = websockets.serve(chat_server, "0.0.0.0", 7123)

print("starting websocket server")
asyncio.get_event_loop().run_until_complete(ws)
asyncio.get_event_loop().run_forever()