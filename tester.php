<?php
// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit();
}

// User is logged in, so proceed to display their profile or perform actions
$email = $_SESSION['email'];

echo "<h2>Welcome back, " . htmlspecialchars($email) . "!</h2>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tester Page</title>
</head>
<body>
    <h1>Transaction History</h1>
    <!-- The rest of your HTML and JavaScript code for transaction history -->
    <div id="transaction-history"></div>

    <script>
        async function fetchTransactionHistory() {
            try {
                const response = await fetch('fetch_transactions.php');
                const data = await response.json();

                const transactionHistoryContainer = document.getElementById('transaction-history');
                transactionHistoryContainer.innerHTML = '';

                if (data.error) {
                    transactionHistoryContainer.innerHTML = `<p>${data.error}</p>`;
                } else {
                    data.forEach(transaction => {
                        transactionHistoryContainer.innerHTML += `
                            <div class="transaction">
                                <p class="amount"><strong>Amount:</strong> ${transaction.amount}</p>
                                <p class="type"><strong>Type:</strong> ${transaction.transaction_type}</p>
                                <p class="type"><strong>Date:</strong> ${transaction.date}</p>
                            </div>
                        `;
                    });
                }
            } catch (error) {
                console.error('Error fetching transaction history:', error);
            }
        }

        window.onload = fetchTransactionHistory;
    </script>
</body>
</html>
