/* ============================================================
   FreshMart — JavaScript global
   - Keranjang via fetch() (AJAX, tanpa reload — rasa SPA)
   - Toast notifikasi
   - Reveal on scroll (IntersectionObserver)
   - Menu mobile & toggle lihat password
   ============================================================ */

const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

/** Format angka menjadi Rupiah, mis. 35000 -> "Rp 35.000" */
function rupiah(n) {
  return 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
}

/** Tampilkan toast notifikasi kecil di pojok kanan bawah. */
function toast(message, type = 'success') {
  let stack = document.getElementById('toast-stack');
  if (!stack) {
    stack = document.createElement('div');
    stack.id = 'toast-stack';
    document.body.appendChild(stack);
  }

  const el = document.createElement('div');
  el.className =
    'toast flex items-center gap-2 rounded-2xl px-4 py-3 text-sm font-semibold ' +
    (type === 'success'
      ? 'bg-leaf-800 text-lime-200'
      : 'bg-tomato text-white');
  el.innerHTML = (type === 'success' ? '🥬 ' : '⚠️ ') + message;
  stack.appendChild(el);

  setTimeout(() => el.classList.add('hide'), 2600);
  setTimeout(() => el.remove(), 3100);
}

/** Update angka badge keranjang di navbar dengan animasi pop. */
function updateCartBadge(count) {
  document.querySelectorAll('[data-cart-count]').forEach((badge) => {
    badge.textContent = count;
    badge.classList.toggle('hidden', count < 1);
    badge.classList.remove('badge-pop');
    void badge.offsetWidth; // paksa reflow agar animasi bisa diulang
    badge.classList.add('badge-pop');
  });
}

/** Kirim request JSON dengan token CSRF. */
async function sendJson(url, method, body = {}) {
  const res = await fetch(url, {
    method,
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': CSRF,
      Accept: 'application/json',
    },
    body: JSON.stringify(body),
  });
  const data = await res.json().catch(() => ({}));
  if (!res.ok) throw new Error(data.message || 'Terjadi kesalahan.');
  return data;
}

/** Tambah produk ke keranjang (dipakai tombol di seluruh situs). */
async function addToCart(productId, qty = 1, button = null) {
  try {
    if (button) {
      button.disabled = true;
      button.classList.add('opacity-60');
    }
    const data = await sendJson(window.FRESHMART.cartAddUrl, 'POST', {
      product_id: productId,
      qty,
    });
    updateCartBadge(data.count);
    toast(data.message);
  } catch (err) {
    toast(err.message, 'error');
  } finally {
    if (button) {
      button.disabled = false;
      button.classList.remove('opacity-60');
    }
  }
}

document.addEventListener('DOMContentLoaded', () => {
  /* ---------- Delegasi tombol "Tambah ke keranjang" ---------- */
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-add-to-cart]');
    if (!btn) return;
    e.preventDefault();

    const qtyInput = document.querySelector('[data-qty-input]');
    const qty = qtyInput ? Math.max(1, parseInt(qtyInput.value || '1', 10)) : 1;
    addToCart(btn.dataset.addToCart, qty, btn);
  });

  /* ---------- Halaman keranjang: ubah qty & hapus (AJAX) ---------- */
  document.addEventListener('click', async (e) => {
    const stepBtn = e.target.closest('[data-cart-step]');
    if (stepBtn) {
      const row = stepBtn.closest('[data-cart-row]');
      const input = row.querySelector('[data-cart-qty]');
      const next = Math.max(
        1,
        parseInt(input.value, 10) + parseInt(stepBtn.dataset.cartStep, 10)
      );
      input.value = next;
      input.dispatchEvent(new Event('change'));
      return;
    }

    const removeBtn = e.target.closest('[data-cart-remove]');
    if (removeBtn) {
      const row = removeBtn.closest('[data-cart-row]');
      try {
        const data = await sendJson(removeBtn.dataset.cartRemove, 'DELETE');
        row.style.transition = 'all .4s ease';
        row.style.opacity = '0';
        row.style.transform = 'translateX(24px)';
        setTimeout(() => {
          row.remove();
          if (data.empty) location.reload();
        }, 380);
        document.querySelectorAll('[data-cart-total]').forEach(
          (el) => (el.textContent = data.total_label)
        );
        updateCartBadge(data.count);
        toast(data.message);
      } catch (err) {
        toast(err.message, 'error');
      }
    }
  });

  document.addEventListener('change', async (e) => {
    const input = e.target.closest('[data-cart-qty]');
    if (!input) return;

    const row = input.closest('[data-cart-row]');
    try {
      const data = await sendJson(input.dataset.cartQty, 'PATCH', {
        qty: Math.max(1, parseInt(input.value || '1', 10)),
      });
      input.value = data.qty;
      row.querySelector('[data-cart-subtotal]').textContent = data.subtotal_label;
      document.querySelectorAll('[data-cart-total]').forEach(
        (el) => (el.textContent = data.total_label)
      );
      updateCartBadge(data.count);
    } catch (err) {
      toast(err.message, 'error');
    }
  });

  /* ---------- Reveal on scroll ---------- */
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.12 }
  );
  document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));

  /* ---------- Menu mobile ---------- */
  const menuBtn = document.querySelector('[data-menu-toggle]');
  const mobileMenu = document.getElementById('mobile-menu');
  if (menuBtn && mobileMenu) {
    menuBtn.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });
  }

  /* ---------- Toggle lihat password ---------- */
  document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.togglePassword);
      if (!input) return;
      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      btn.textContent = show ? '🙈' : '👁️';
    });
  });

  /* ---------- Sidebar admin (mobile) ---------- */
  const sideBtn = document.querySelector('[data-sidebar-toggle]');
  const sidebar = document.getElementById('admin-sidebar');
  if (sideBtn && sidebar) {
    sideBtn.addEventListener('click', () => {
      sidebar.classList.toggle('-translate-x-full');
    });
  }
});
