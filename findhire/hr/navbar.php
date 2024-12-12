<div class="navbar">
    <div class="navbar-content">
        <img src="findL.png" alt="Logo">
		<h3>
			<a href="index.php">Home</a>
			<a href="insertpost.php">Add New Post</a>
			<a href="register-an-hr.php">Add New HR</a>
			<a href="allhrs.php">All HRs</a>
			<a href="allusers.php">All Users</a>
			<a href="activitylogs.php">Activity Logs</a>
			<a href="core/handleForms.php?logoutUserBtn=1">Logout</a>	
		</h3>	
    </div>
    <h1>Hello, <span class="username-highlight"><?php echo $_SESSION['username']; ?></span>. Welcome to the FindHire HR Management Dashboard!</h1>
</div>
