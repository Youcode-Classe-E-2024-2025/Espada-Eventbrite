
 let ws;
 let user_id; 
 
 // Function to connect to WebSocket server
//  function connectToWebSocket() {
     ws = new WebSocket('ws://localhost:3009'); // Ensure it's connecting to 3009
 
     // When the connection is open, send a registration message
     ws.onopen = () => {
         console.log('Connected to WebSocket server');
         user_id = Math.floor(Math.random() * 500);  // Generate a random user ID
         const registerMessage = { type: 'register', user_id: user_id };
         ws.send(JSON.stringify(registerMessage));  // Send the registration message
     };
 
     // When a message is received from the server
     ws.onmessage = (event) => {
         const data = JSON.parse(event.data);
         console.log('Received from server:', data);
         document.getElementById('messageArea').innerText = JSON.stringify(data, null, 2);
     };
 
     // When the connection is closed
     ws.onclose = () => {
         console.log('Disconnected from WebSocket server');
     };
 
     // When there's an error
     ws.onerror = (error) => {
         console.error('WebSocket error:', error);
     };
//  }
 
//  // Function to send a message to another user
//  function sendMessage() {
//      const reIdValue = document.getElementById('re_id').value;
//      const contentValue = document.getElementById('content').value;
    
//      if (!reIdValue || !contentValue) {
//          alert('Please provide both recipient ID and message content.');
//          return;
//      }
 
//      // Send the message to the server
//      const message = {
//          type: 'email',
//          id: user_id,        // Include the sender's user ID
//          re_id: reIdValue,   // Recipient ID
//          content: contentValue // Message content
//      };
 
//      ws.send(JSON.stringify(message));  // Send the message to the WebSocket server
 
//      // Clear the input fields after sending
//      document.getElementById('re_id').value = '';
//      document.getElementById('content').value = '';
//  }