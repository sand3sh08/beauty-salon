<?php include 'includes/header.php'; ?>

<!-- ===== PAGE HEADER ===== -->
<section class="page-header" style="background: var(--ink); padding: 140px 0 60px; text-align: center; color: var(--cream);">
  <div class="wrap">
    <p class="eyebrow" style="color: var(--gold); margin-bottom: 10px;">Take the house home</p>
    <h1 style="font-family: 'Fraunces', serif; font-size: 3rem; margin: 0; font-weight: 500; font-style: italic;">Shop Our Shelf</h1>
  </div>
</section>

<!-- ===== PRODUCTS ===== -->
<section class="products" id="products" style="padding: 80px 0;">
  <div class="wrap">
    <div class="product-grid" id="productGrid">
      <!-- Products are loaded dynamically by script.js. The static cards below act as a premium fallback on first paint or DB failure. -->
      <article class="product-card" data-id="1" data-name="Botanical Shampoo" data-price="650">
        <div class="product-frame"><img src="images/products/shampoo.jpg" alt="Botanical Shampoo"></div>
        <h3>Botanical Shampoo</h3>
        <p class="product-price">₹650</p>
        <button class="btn btn-line buy-product" type="button">Buy now</button>
      </article>

      <article class="product-card" data-id="2" data-name="Repair Hair Serum" data-price="890">
        <div class="product-frame"><img src="images/products/hair-serum.jpg" alt="Repair Hair Serum"></div>
        <h3>Repair Hair Serum</h3>
        <p class="product-price">₹890</p>
        <button class="btn btn-line buy-product" type="button">Buy now</button>
      </article>

      <article class="product-card" data-id="3" data-name="Gentle Face Wash" data-price="450">
        <div class="product-frame"><img src="images/products/face-wash.jpg" alt="Gentle Face Wash"></div>
        <h3>Gentle Face Wash</h3>
        <p class="product-price">₹450</p>
        <button class="btn btn-line buy-product" type="button">Buy now</button>
      </article>

      <article class="product-card" data-id="4" data-name="Daily Moisturizer" data-price="720">
        <div class="product-frame"><img src="images/products/moisturizer.jpg" alt="Daily Moisturizer"></div>
        <h3>Daily Moisturizer</h3>
        <p class="product-price">₹720</p>
        <button class="btn btn-line buy-product" type="button">Buy now</button>
      </article>

      <article class="product-card" data-id="5" data-name="Signature Lipstick" data-price="550">
        <div class="product-frame"><img src="images/products/lipstick.jpg" alt="Signature Lipstick"></div>
        <h3>Signature Lipstick</h3>
        <p class="product-price">₹550</p>
        <button class="btn btn-line buy-product" type="button">Buy now</button>
      </article>

      <article class="product-card" data-id="6" data-name="Second-Skin Foundation" data-price="980">
        <div class="product-frame"><img src="images/products/foundation.jpg" alt="Second-Skin Foundation"></div>
        <h3>Second-Skin Foundation</h3>
        <p class="product-price">₹980</p>
        <button class="btn btn-line buy-product" type="button">Buy now</button>
      </article>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
