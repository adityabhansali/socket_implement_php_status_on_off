<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebSocket Client</title>
</head>
<body>

<div id="adminStatus">Admin status: Unknown</div>

<script>
    function connectWebSocket() {
        const socket = new WebSocket('ws://localhost:8080');

        socket.addEventListener('open', function (event) {
            updateAdminStatus('Admin is online');
        });

        socket.addEventListener('message', function (event) {
            const data = JSON.parse(event.data);
            if (data.adminOnline) {
                updateAdminStatus('Admin is online');
            } else {
                updateAdminStatus('Admin is offline');
            }
        });

        socket.addEventListener('error', function (event) {
            updateAdminStatus('Admin is offline');
            socket.close(); // Close the socket on error
        });

        socket.addEventListener('close', function (event) {
            updateAdminStatus('Admin is offline');
            // Attempt to reconnect after a delay (e.g., 5 seconds)
            setTimeout(connectWebSocket, 5000);
        });

        function updateAdminStatus(status) {
            const adminStatusDiv = document.getElementById('adminStatus');
            adminStatusDiv.textContent = status;
        }
    }

    // Initial connection
    connectWebSocket();
</script>

</body>
</html>

