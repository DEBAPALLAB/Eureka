<div class="sidebar">
  <div class="overlay"></div> <!--Transparent layer behind content -->

  <!-- Sidebar Content -->
  <h2 class="logo">Quizzy</h2>
  <a class="sidebar-btn" href="index.php">🏠 Home</a>
  <a class="sidebar-btn" href="stats.php">📊 Stats</a>
  <a class="sidebar-btn" href="register.php">📝 Register</a>

  <!-- Logout Button at Bottom -->
  <a class="sidebar-btn logout-btn" href="../auth/logout.php">🚪 Logout</a>
</div>

<style>
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 220px;
  height: 100vh;
  background: url('side-bg.jpeg') no-repeat center center;
  background-size: cover;
  overflow: hidden;
  z-index: 1000;
  padding: 30px 20px;
  display: flex;
  flex-direction: column;

  /* Rounded right corners */
  border-top-right-radius: 40px;
  border-bottom-right-radius: 40px;
}

/* Ensure logo and buttons are ABOVE overlay */
.sidebar > *:not(.overlay) {
  position: relative;
  z-index: 2;
}

/* Logo style */
.sidebar .logo {
  color: orange;
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 2rem;
}

/* Button styles */
.sidebar-btn {
  display: block;
  margin: 10px 0;
  padding: 10px 15px;
  background-color: orange;
  color: black;
  border-radius: 12px;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.95rem;
  transition: background 0.3s;
}

.sidebar-btn:hover {
  background-color: #e69500;
}

/* Logout button at the bottom */
.logout-btn {
  margin-top: auto;
  background-color: #e74c3c;
  color: white;
}

.logout-btn:hover {
  background-color: #c0392b;
}
</style>
