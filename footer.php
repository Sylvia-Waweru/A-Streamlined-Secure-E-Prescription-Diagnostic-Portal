<footer class="footer py-5 text-white text-center neon-footer">
  <div class="container">
    <div class="row gy-4 justify-content-center align-items-start text-center">

      <!-- Logo + Branding -->
      <div class="col-lg-4">
        <h3 class="footer-logo">⚡ E-Prescription</h3>
        <p class="small">Smart • Seamless • Secure<br>Empowering Doctors, Patients & Pharmacists.</p>
      </div>

      <!-- Navigation Links -->
     <div class="col-lg-4">
  <div class="footer-links d-flex flex-column gap-2 align-items-center">
    <a href="https://www.instagram.com" target="_blank" class="d-flex align-items-center gap-2">
      <i class="fab fa-instagram"></i> <span>Instagram</span>
    </a>
    <a href="https://twitter.com" target="_blank" class="d-flex align-items-center gap-2">
  <i class="fab fa-twitter"></i> <span>X (Twitter)</span>
</a>

    <a href="https://github.com" target="_blank" class="d-flex align-items-center gap-2">
      <i class="fab fa-github"></i> <span>GitHub</span>
    </a>
  </div>
</div>


      <!-- Contact Section -->
      <div class="col-lg-4">
        <h6 class="fw-bold glow-text">📬 Contact</h6>
        <p class="small mb-1"><i class="fas fa-envelope me-2"></i> support@e-prescription.com</p>
        <p class="small"><i class="fas fa-phone me-2"></i> +254 717931525</p>
      </div>

    </div>

    <hr class="footer-line my-4">
    <p class="small mb-0 glow-text">&copy; <?= date('Y') ?> E-Prescription Portal. All rights reserved.</p>
  </div>
</footer>

<style>
    html, body {
  height: 100%;
  margin: 0;
  display: flex;
  flex-direction: column;
}

body {
  min-height: 100vh;
}

main {
  flex: 1; /* Fills remaining space */
}

  .neon-footer {
    background: linear-gradient(135deg,rgb(0, 14, 11),rgb(0, 19, 19));
    backdrop-filter: blur(12px);
    border-top: 2px solid rgba(0, 255, 230, 0.3);
    font-family: 'Rubik', sans-serif;
    animation: fadeIn 1s ease-in;
    color: #e0e0e0;
    text-align: center;
  }

  .footer-logo {
    font-weight: 700;
    color: #00ffe7;
    text-shadow: 0 0 8pxrgb(2, 58, 52), 0 0 20pxrgb(2, 36, 32);
  }

  .footer-links a {
    color: #00ffe7;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease-in-out;
  }

  .footer-links a:hover {
    color: #ffffff;
    text-shadow: 0 0 5px #00ffe7;
    transform: translateX(5px);
  }

  .glow-text {
    color: #00ffe7;
    text-shadow: 0 0 5pxrgb(1, 32, 29), 0 0 10px #00ffe7;
  }

  .footer-line {
    border-color: rgba(0, 255, 230, 0.4);
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @media (max-width: 768px) {
    .footer .col-lg-4 {
      margin-bottom: 30px;
    }
  }
</style>
<script src="https://kit.fontawesome.com/a2e5e6e6d2.js" crossorigin="anonymous"></script>
