<?php include 'includes/header.php'; ?>

<!-- ===== PAGE HEADER ===== -->
<section class="page-header" style="background: var(--ink); padding: 140px 0 60px; text-align: center; color: var(--cream);">
  <div class="wrap">
    <p class="eyebrow" style="color: var(--gold); margin-bottom: 10px;">Visit us</p>
    <h1 style="font-family: 'Fraunces', serif; font-size: 3rem; margin: 0; font-weight: 500; font-style: italic;">Reserve Your Chair</h1>
  </div>
</section>

<!-- ===== BOOKING / CONTACT ===== -->
<section class="book" id="book" style="padding: 80px 0;">
  <div class="wrap book-grid">
    <div class="book-info">
      <p class="eyebrow eyebrow-dark">Visit us</p>
      <h2>Reserve your chair.</h2>
      <p class="book-lead">Tell us what you're after and we'll confirm a slot within the day.
        Walk-ins welcome, but a reservation means no waiting.</p>

      <ul class="info-list">
        <li><strong>Address</strong><span>14 Church Street, Bengaluru, Karnataka 560001</span></li>
        <li><strong>Hours</strong><span>Tue – Sun, 10:00 AM – 8:00 PM · Closed Mondays</span></li>
        <li><strong>Phone</strong><span>+91 98765 43210</span></li>
        <li><strong>Email</strong><span>hello@verbenaandco.in</span></li>
      </ul>

      <div class="social-row">
        <a href="#" aria-label="Instagram">Instagram</a>
        <a href="#" aria-label="Facebook">Facebook</a>
        <a href="#" aria-label="WhatsApp">WhatsApp</a>
      </div>
    </div>

    <form class="book-form" id="bookForm" novalidate>
      <div class="form-row">
        <label for="name">Full name</label>
        <input type="text" id="name" name="name" placeholder="Your name" required>
      </div>
      <div class="form-row two-col">
        <div>
          <label for="phone">Phone</label>
          <input type="tel" id="phone" name="phone" placeholder="+91 00000 00000" required>
        </div>
        <div>
          <label for="date">Preferred date</label>
          <input type="date" id="date" name="date" required>
        </div>
      </div>
      <div class="form-row">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="you@email.com">
      </div>
      <div class="form-row">
        <label for="service">Service</label>
        <select id="service" name="service" required>
          <option value="" disabled selected>Choose a service</option>
          <option>Hair</option>
          <option>Skin</option>
          <option>Makeup</option>
          <option>Spa</option>
        </select>
      </div>
      <div class="form-row">
        <label for="notes">Anything we should know?</label>
        <textarea id="notes" name="notes" rows="3" placeholder="Allergies, occasion, stylist preference..."></textarea>
      </div>
      <button type="submit" class="btn btn-gold full">Request appointment</button>
      <p class="form-status" id="formStatus" role="status" aria-live="polite"></p>
    </form>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
