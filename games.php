<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&display=swap"
    />
    <title>Car Racing</title>
    <link rel="stylesheet" href="./css/homePage.css" />
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <style>
      /* Loader styles */
      .loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        font-family: 'Luckiest Guy', cursive;
        font-size: 2rem;
        color: #333;
      }
      .loader.hidden {
        display: none;
      }
    </style>
  </head>

  <body>
    <!-- Loader -->
    <div class="loader" id="loader">Loading...</div>

    <div class="container">
      <div class="child">
        <h1 class="game_title">Car Racing</h1>
        <div class="my_buttons">
          <button
            class="start_btn"
            id="start_btn"
            onclick="redirectToGames()"
          >
            Play Now
          </button>
          <button
            class="score_btn"
            id="score_btn"
            onclick="location.href = 'scores.html'"
          >
            Score Records
          </button>
          <button class="guide_btn" id="guide_btn">
            How to Play?
          </button>
        </div>
        <div class="guide" id="guide">
          <p>Use The Arrow Keys or WASD Keys For Navigation.</p>
          <pre>&uarr; | W : Move Forward</pre>
          <pre>&darr; | S : Move Backward</pre>
          <pre>&larr; | A : Move Left</pre>
          <pre>&rarr; | D : Move Right</pre>
          <br />
          <p>Also, You Can Navigate Through Your Touchpad.</p>
          <button
            class="guide_back_btn"
            id="guide_back_btn"
          >
            Exit
          </button>
        </div>
      </div>
    </div>
    <script>
      // Show loader and redirect
      function redirectToGames() {
        const loader = document.getElementById('loader');
        loader.classList.remove('hidden'); // Show loader
        setTimeout(() => {
          location.href = 'game/index.html'; // Redirect after 2 seconds
        }, 2000);
      }

      // Hide loader on page load
      window.onload = () => {
        const loader = document.getElementById('loader');
        loader.classList.add('hidden'); // Hide loader
      };
    </script>
  </body>
</html>