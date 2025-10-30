<header>
    <h1>AYOKO NA</h1>
    <nav>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="admin_users.php">Users</a>
        <a href="admin_reports.php">Reports</a>
        <a href="admin_rants.php">Rants</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<style>
    body {
        margin: 0;
        font-family: "Poppins", sans-serif;
        background-color: #d3d3d3;
    }

    header {
        background-color: #8753a1;
        color: white;
        padding: 20px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    header h1 {
        font-weight: 600;
        letter-spacing: 2px;
    }

    nav a {
        color: white;
        text-decoration: none;
        margin: 0 15px;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    nav a:hover {
        text-decoration: underline;
    }

    .container {
        padding: 40px;
    }

    .card {
        background-color: #e3e3e3;
        border: 2px solid #8753a1;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
    }

    table, th, td {
        border: 1px solid #8753a1;
    }

    th, td {
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: #8753a1;
        color: white;
    }

    .btn {
        border: 2px solid #8753a1;
        background: none;
        color: #8753a1;
        padding: 5px 15px;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn:hover {
        background: #8753a1;
        color: white;
    }
</style>
