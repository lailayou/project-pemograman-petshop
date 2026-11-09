
document.addEventListener('DOMContentLoaded', function() {
  fetch('navbar.php')
    .then(response => response.text())
    .then(data => {
      document.getElementById('navbar-placeholder').innerHTML = data;
    });
});

