<div class="navbar">
    <div class="navbar-content">
        <img src="findL.png" alt="Logo">
		<h3>
			<a href="index.php">Home</a>
			<a href="profile.php?user_id=<?php echo $_SESSION['user_id']; ?>">Profile</a>
			<a href="core/handleForms.php?logoutUserBtn=1">Logout</a>	
		</h3>	
    </div>
    <h1>Hello, <span class="username-highlight"><?php echo $_SESSION['username']; ?></span>. Welcome to FindHire, where your potential meets opportunity!</h1>
</div>
