<html>
    <head>
        <title>Webchat</title>
        <meta name=viewport content="width=device-width, initial-scale=1">
        <script>
            let webChat;
            let myName;
            let webchatServer = "ws://192.168.0.108:7123";

            function on_load() {
                document.getElementById('myname').onkeyup = function(event) {
                    event.preventDefault();
                    if (event.keyCode === 13) {
                        myName = document.getElementById('myname').value;
                        document.getElementById('entername').style = "display: none;";
                        document.getElementById('chat').style = "display: block;";
                        init_chat();
                    }
                };
            }

            function init_chat() {
                inboxWrite("webchat", "connecting to " + webchatServer);
                webChat = new WebSocket(webchatServer);
                webChat.onopen = function(event) {
                    inboxWrite("webchat", "connected!");
                    send("joined!");
                };

                webChat.onerror = function(event) {
                    inboxWrite("webchat", "cannot connect to server!");
                };

                webChat.onmessage = function(event) {
                    let msg = JSON.parse(event.data);
                    from = msg.sender;
                    payload = msg.payload;
                    inboxWrite(from, payload);
                };

                document.getElementById('msg').onkeyup = function(event) {
                    event.preventDefault();
                    if (event.keyCode === 13) {
                        send(document.getElementById('msg').value);
                        document.getElementById('msg').value = "";
                    }
                };
            }

            function send(text) {
                let msg = {
                    sender: myName,
                    payload: text
                };
                webChat.send(JSON.stringify(msg));
            }

            function inboxWrite(from, msg) {
                let inbox = document.getElementById('inbox');
                inbox.innerHTML += '<b>[' + from + ']:</b> ' + msg + '<br>';
                inbox.scrollTop = inbox.scrollHeight;
            }
        </script>
        <style>
            * {
                font-family: open-sans, sans-serif;
            }

            #inbox {
                border: 1px solid black;
                border-radius: 3px;
                height: 300px;
                overflow: scroll;
            }

            article {
                width: 800px;
                margin: auto;
            }

            article input {
                width: 100%;
            }

            @media (max-width: 800px) { 
                article {
                    width: 100%;
                }
            }
        </style>
    </head>
    <body onload="on_load()">
        <article id="chat" style="display: none;">
            <h2>ULMER WebChat</h2>
            <div id="inbox"></div>
            <input type="text" placeholder="Nachricht" id="msg" />
        </article>
        <article id="entername">
            <p>Bitte gib deinen Namen ein und drücke Enter!</p>
            <p><input type="text" placeholder="Dein Name" id="myname" /></p>
        </article>
    </body>
</html>