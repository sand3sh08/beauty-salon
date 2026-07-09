const API_BASE = 'backend/api';

document.addEventListener('DOMContentLoaded', () => {

  /* ---------- Sticky header state ---------- */
  const header = document.getElementById('siteHeader');
  const toggleHeaderState = () => {
    if (header) {
      header.classList.toggle('scrolled', window.scrollY > 40);
    }
  };
  toggleHeaderState();
  window.addEventListener('scroll', toggleHeaderState, { passive: true });

  /* ---------- Mobile nav ---------- */
  const navToggle = document.getElementById('navToggle');
  const mainNav = document.getElementById('mainNav');

  const closeNav = () => {
    if (mainNav) mainNav.classList.remove('open');
    if (navToggle) {
      navToggle.classList.remove('open');
      navToggle.setAttribute('aria-expanded', 'false');
    }
  };

  if (navToggle && mainNav) {
    navToggle.addEventListener('click', () => {
      const isOpen = mainNav.classList.toggle('open');
      navToggle.classList.toggle('open', isOpen);
      navToggle.setAttribute('aria-expanded', String(isOpen));
    });

    mainNav.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', closeNav);
    });
  }

  /* ---------- Back to top ---------- */
  const backToTop = document.getElementById('backToTop');
  if (backToTop) {
    window.addEventListener('scroll', () => {
      backToTop.classList.toggle('visible', window.scrollY > 600);
    }, { passive: true });
    backToTop.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ---------- Toast helper ---------- */
  function showToast(message) {
    let toast = document.querySelector('.toast');
    if (!toast) {
      toast = document.createElement('div');
      toast.className = 'toast';
      document.body.appendChild(toast);
    }
    toast.textContent = message;
    requestAnimationFrame(() => toast.classList.add('show'));
    clearTimeout(showToast._t);
    showToast._t = setTimeout(() => toast.classList.remove('show'), 2600);
  }

  /* ---------- Product shop ---------- */
  const productGrid = document.getElementById('productGrid');
  let currentPurchaseProduct = null;

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  function renderProducts(products) {
    if (!productGrid) return;
    productGrid.innerHTML = products.map(p => `
      <article class="product-card" data-id="${p.id}" data-name="${escapeHtml(p.name)}" data-price="${Number(p.price)}">
        <div class="product-frame"><img src="${p.image}" alt="${escapeHtml(p.name)}"></div>
        <h3>${escapeHtml(p.name)}</h3>
        <p class="product-price">₹${Number(p.price).toLocaleString('en-IN')}</p>
        <button class="btn btn-line buy-product" type="button">Buy now</button>
      </article>
    `).join('');
    bindBuyButtons();
  }

  function loadProducts() {
    fetch(`${API_BASE}/get_products.php`)
      .then(res => res.json())
      .then(data => {
        if (data.success && data.products.length) renderProducts(data.products);
      })
      .catch(() => { /* keep the static markup already in the page as a fallback */ });
  }

  function createPurchaseModal() {
    if (document.getElementById('buyModal')) return;

    const modal = document.createElement('div');
    modal.id = 'buyModal';
    modal.className = 'modal-backdrop';
    modal.innerHTML = `
      <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="buyModalTitle">
        <h3 id="buyModalTitle">Buy now</h3>
        <p class="modal-copy">Choose the quantity and confirm your order instantly.</p>
        <form id="buyForm" class="buy-form">
          <div class="quantity-row">
            <label for="buyQuantity">Quantity</label>
            <input id="buyQuantity" name="quantity" type="number" min="1" value="1" required>
          </div>
          <div class="modal-price-row">
            <span>Total</span>
            <strong data-role="total">₹0</strong>
          </div>
          <div class="modal-actions">
            <button type="button" class="btn btn-line" id="cancelBuy">Cancel</button>
            <button type="submit" class="btn btn-gold">Confirm order</button>
          </div>
        </form>
      </div>
    `;
    document.body.appendChild(modal);

    modal.addEventListener('click', (event) => {
      if (event.target === modal) closePurchaseModal();
    });

    document.getElementById('cancelBuy').addEventListener('click', closePurchaseModal);
    document.getElementById('buyForm').addEventListener('submit', (event) => {
      event.preventDefault();
      if (!currentPurchaseProduct) return;

      const formData = new FormData(event.currentTarget);
      formData.set('product_id', currentPurchaseProduct.id);

      fetch(`${API_BASE}/place_order.php`, {
        method: 'POST',
        body: formData,
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            showToast(`${data.message}. Total: ₹${data.total}`);
            closePurchaseModal();
          } else {
            showToast(data.message || 'We could not place your order.');
          }
        })
        .catch(() => showToast('We could not place your order. Please try again.'));
    });
  }

  function openPurchaseModal(product) {
    createPurchaseModal();
    currentPurchaseProduct = product;
    const modal = document.getElementById('buyModal');
    if (!modal) return;
    const title = modal.querySelector('#buyModalTitle');
    const quantityInput = modal.querySelector('#buyQuantity');
    const totalEl = modal.querySelector('[data-role="total"]');

    if (title) title.textContent = `Buy ${product.name}`;
    if (quantityInput) {
      quantityInput.value = '1';
      const syncTotal = () => {
        const quantity = Math.max(1, Number(quantityInput.value) || 1);
        if (totalEl) totalEl.textContent = `₹${(quantity * product.price).toLocaleString('en-IN')}`;
      };
      quantityInput.removeEventListener('input', syncTotal);
      quantityInput.addEventListener('input', syncTotal);
      syncTotal();
    }
    modal.classList.add('show');
  }

  function closePurchaseModal() {
    const modal = document.getElementById('buyModal');
    if (modal) modal.classList.remove('show');
  }

  function bindBuyButtons() {
    document.querySelectorAll('.buy-product').forEach(btn => {
      // Remove any existing listener to prevent duplicate binding
      const newBtn = btn.cloneNode(true);
      btn.parentNode.replaceChild(newBtn, btn);
      newBtn.addEventListener('click', () => {
        const card = newBtn.closest('.product-card');
        const product = {
          id: card?.dataset.id || '',
          name: card?.dataset.name || 'Item',
          price: Number(card?.dataset.price || 0),
        };

        if (!product.id) {
          alert('This product is temporarily unavailable.');
          return;
        }

        fetch(`${API_BASE}/get_session.php`)
          .then(res => res.json())
          .then(data => {
            if (!data.loggedIn) {
              window.location.href = 'backend/auth/login.php';
              return;
            }
            openPurchaseModal(product);
          })
          .catch(() => {
            window.location.href = 'backend/auth/login.php';
          });
      });
    });
  }

  if (productGrid) {
    loadProducts();
    bindBuyButtons(); // also bind the static fallback cards on first paint
  }

  /* ---------- Booking form ---------- */
  const bookForm = document.getElementById('bookForm');
  const formStatus = document.getElementById('formStatus');

  if (bookForm) {
    bookForm.addEventListener('submit', (e) => {
      e.preventDefault();
      if (!bookForm.checkValidity()) {
        bookForm.reportValidity();
        return;
      }

      const submitBtn = bookForm.querySelector('button[type="submit"]');
      if (submitBtn) submitBtn.disabled = true;
      if (formStatus) formStatus.textContent = 'Checking your account…';

      fetch(`${API_BASE}/get_session.php`)
        .then(res => res.json())
        .then(data => {
          if (!data.loggedIn) {
            window.location.href = 'backend/auth/login.php';
            return;
          }

          if (formStatus) formStatus.textContent = 'Sending your request…';
          const formData = new FormData(bookForm);

          fetch(`${API_BASE}/book_appointment.php`, {
            method: 'POST',
            body: formData,
          })
            .then(res => res.json())
            .then(data => {
              if (formStatus) formStatus.textContent = data.message;
              if (data.success) {
                alert(data.message);
                bookForm.reset();
              }
            })
            .catch(() => {
              if (formStatus) formStatus.textContent = "Something went wrong on our end — please call us instead.";
            })
            .finally(() => { if (submitBtn) submitBtn.disabled = false; });
        })
        .catch(() => {
          if (formStatus) formStatus.textContent = 'Please log in to book an appointment.';
          if (submitBtn) submitBtn.disabled = false;
        });
    });
  }

  /* ---------- Newsletter form ---------- */
  const newsletterForm = document.getElementById('newsletterForm');
  if (newsletterForm) {
    newsletterForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const emailInput = newsletterForm.querySelector('input[type="email"]');
      if (!emailInput) return;

      fetch(`${API_BASE}/subscribe_newsletter.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `email=${encodeURIComponent(emailInput.value)}`,
      })
        .then(res => res.json())
        .then(data => showToast(data.message))
        .catch(() => showToast("You're on the list — welcome to the house."))
        .finally(() => newsletterForm.reset());
    });
  }

  /* ---------- Header auth state ---------- */
  const authLink = document.getElementById('authLink');
  const registerLink = document.getElementById('registerLink');

  function updateAuthState(user) {
    if (!authLink) return;
    if (user && user.username) {
      authLink.textContent = `Hello, ${user.username}`;
      authLink.href = 'backend/auth/logout.php';
      authLink.classList.add('is-logged-in');
      if (registerLink) {
        registerLink.textContent = 'Logout';
        registerLink.href = 'backend/auth/logout.php';
        registerLink.classList.add('is-logged-in');
      }
    } else {
      authLink.textContent = 'Login';
      authLink.href = 'backend/auth/login.php';
      authLink.classList.remove('is-logged-in');
      if (registerLink) {
        registerLink.textContent = 'Register';
        registerLink.href = 'backend/auth/register.php';
        registerLink.classList.remove('is-logged-in');
      }
    }
  }

  fetch(`${API_BASE}/get_session.php`)
    .then(res => res.json())
    .then(data => updateAuthState(data.user))
    .catch(() => updateAuthState(null));

  /* ---------- Footer year ---------- */
  const yearEl = document.getElementById('year');
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  /* ---------- Set min date for booking to today ---------- */
  const dateInput = document.getElementById('date');
  if (dateInput) {
    const today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('min', today);
  }
});
