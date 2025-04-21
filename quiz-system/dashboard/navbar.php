<!-- dashboard/navbar.php -->
<div class="sidebar" id="sidebar">
  <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
  <h2 id="logo-text">Quizzy</h2>
  <a href="/quiz-system/dashboard/index.php" title="Home">🏠 <span class="link-text">Home</span></a>
  <a href="quizzes/stats.php" title="Stats">📊 <span class="link-text">Stats</span></a>
  <a href="/quiz-system/dashboard/quizzes/manage.php" title="Manage Quizzes">📝 <span class="link-text">Manage Quizzes</span></a>
</div>

<style>
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 200px;
    height: 100%;
    background-color: #4e73df;
    color: white;
    padding: 30px 20px;
    font-family: 'Rubik', sans-serif;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    z-index: 1000;
    transition: width 0.3s ease;
    overflow: hidden;
  }

  .sidebar.minimized {
    width: 70px;
    padding: 30px 10px;
  }

  .toggle-btn {
    background: none;
    border: none;
    color: white;
    font-size: 1.4rem;
    cursor: pointer;
    margin-bottom: 1.5rem;
    padding: 5px;
  }

  #logo-text {
    font-size: 1.8rem;
    margin-bottom: 2rem;
    font-weight: 700;
    transition: opacity 0.3s ease;
  }

  .sidebar.minimized #logo-text {
    opacity: 0;
    pointer-events: none;
  }

  .sidebar a {
    display: flex;
    align-items: center;
    color: white;
    text-decoration: none;
    margin-bottom: 1.2rem;
    font-size: 1.1rem;
    transition: padding-left 0.3s ease;
    white-space: nowrap;
  }

  .sidebar a:hover {
    padding-left: 10px;
    color: #ffefc3;
  }

  .sidebar .link-text {
    margin-left: 10px;
    transition: opacity 0.3s ease;
  }

  .sidebar.minimized .link-text {
    opacity: 0;
    width: 0;
    overflow: hidden;
    pointer-events: none;
  }
</style>

<script>
  function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const wrapper = document.querySelector(".content-wrapper");
    sidebar.classList.toggle("minimized");
    wrapper.style.marginLeft = sidebar.classList.contains("minimized") ? "70px" : "220px";
  }
</script>
