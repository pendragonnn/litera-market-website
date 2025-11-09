<section id="ourCollection" class="container mx-auto px-6 mb-16">
  <h3 class="text-center text-2xl font-bold text-[#1B3C53] mb-3">Our Collection</h3>
  <p class="text-center text-gray-600 mb-8">Find a wide selection of interesting books only at LiteraMarket.</p>
  <div class="grid grid-cols-2 md:grid-cols-3 gap-10">
    @forelse ($books as $book)
      <div
        class="border border-gray-300 bg-[#f9f9f9] flex flex-col items-center shadow-sm rounded-md hover:shadow-md transition-all">

        {{-- Gambar Buku --}}
        <div
          class="w-full h-[280px] flex justify-center items-center bg-white border-b border-gray-300 overflow-hidden rounded-t-md">
          <img src="{{ $book->image ? asset($book->image) : 'https://placehold.co/200x300?text=No+Image' }}"
            alt="{{ $book->title }}"
            class="w-[70%] h-full object-contain transition-transform duration-300 hover:scale-105">
        </div>

        {{-- Informasi Buku --}}
        <div class="w-full p-4 flex flex-col justify-between flex-grow text-left bg-gray-50 border-t border-gray-200">
          <div>
            <h4 class="text-base font-bold text-gray-800 truncate">{{ $book->title }}</h4>
            <p class="text-sm text-gray-600 mb-2">{{ $book->author }}</p>
          </div>

          <div class="mt-auto flex flex-col sm:flex-row justify-between items-center gap-2 border-t border-gray-200 pt-3">
            <div class="flex gap-2">
              {{-- Tombol Detail --}}
              <button
                class="bg-[#002D72] text-white text-xs sm:text-sm font-medium px-3 py-1.5 rounded-md hover:bg-[#001E4D] transition"
                data-book-id="{{ $book->id }}" data-title="{{ $book->title }}" data-author="{{ $book->author }}"
                data-description="{{ $book->description }}" data-price="{{ $book->price ?? 0 }}"
                data-stock="{{ $book->stock }}" data-image="{{ $book->image ?? asset('images/default-book.jpg') }}"
                onclick="openBookModal(this)">
                detail
              </button>

              {{-- Tombol Add to Cart --}}
              @if ($book->stock > 0)
                <button
                  class="add-to-cart bg-[#2E7D32] text-white text-xs sm:text-sm font-medium px-3 py-1.5 rounded-md hover:bg-[#1B5E20] transition"
                  data-book-id="{{ $book->id }}" data-title="{{ $book->title }}" data-price="{{ $book->price ?? 0 }}"
                  data-stock="{{ $book->stock }}" data-image="{{ $book->image ?? asset('images/default-book.jpg') }}">
                  🛒
                </button>
              @else
                <span class="italic text-red-600 text-xs px-2 py-1 rounded-md font-medium whitespace-nowrap">
                  Out of Stock
                </span>
              @endif
            </div>

            {{-- Harga --}}
            <span class="text-[#C0392B] font-semibold text-sm sm:text-base whitespace-nowrap">
              Rp {{ number_format($book->price ?? 0, 0, ',', '.') }}
            </span>
          </div>
        </div>
      </div>
    @empty
      <p class="text-center col-span-full text-gray-500">No books found.</p>
    @endforelse
  </div>

  {{-- Pagination --}}
  <div class="mt-10 flex justify-center">
    {{ $books->links('components.pagination.pagination') }}
  </div>
</section>

{{-- === Notification Modal (Success/Error) === --}}
<div id="cartAlert" class="fixed inset-0 hidden mt-4 items-start justify-center z-50 transition-all duration-300">
  <div
    class="bg-[#F9F3EF] border border-[#d2c1b6]/70 rounded-xl shadow-xl w-[90%] max-w-md transform scale-95 opacity-0 transition-all duration-300"
    id="cartAlertBox">
    <div class="border-b border-[#d2c1b6]/60 px-4 py-3 flex justify-between items-center rounded-t-xl">
      <h2 class="font-semibold text-[#1B3C53]">LiteraMarket</h2>
      <button id="closeAlertBtn" class="text-[#1B3C53]/60 hover:text-[#1B3C53] text-sm font-bold">✕</button>
    </div>
    <div class="px-5 py-4 text-start">
      <p id="cartAlertText" class="text-[#1B3C53] text-sm font-medium"></p>
    </div>
  </div>
</div>

{{-- === Book Detail Modal === --}}
<div id="bookModal"
  class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 transition-all duration-300 ease-out">
  <div
    class="bg-white rounded-2xl shadow-2xl w-[92%] sm:w-[85%] lg:w-[70%] max-w-4xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300"
    id="bookModalBox">

    {{-- Header --}}
    <div class="flex justify-between items-center border-b border-gray-200 px-5 sm:px-8 py-4 bg-[#F9F3EF]">
      <h2 class="text-lg sm:text-xl md:text-2xl font-semibold text-[#1B3C53]" id="modalBookTitle">Book Title</h2>
      <button onclick="closeBookModal()"
        class="text-[#1B3C53]/70 hover:text-[#1B3C53] text-lg sm:text-xl font-bold transition duration-150">
        ✕
      </button>
    </div>

    {{-- Body --}}
    <div class="grid md:grid-cols-2 gap-6 p-5 sm:p-8 items-center">
      {{-- Image Section --}}
      <div class="flex justify-center items-center">
        <img id="modalBookImage" src="{{ asset('images/default-book.jpg') }}" alt="Book Image"
          class="h-56 sm:h-72 md:h-80 object-contain rounded-lg shadow-sm transition-transform duration-300 hover:scale-105">
      </div>

      {{-- Info Section --}}
      <div>
        <h3 class="font-bold text-[#1B3C53] text-lg sm:text-xl md:text-2xl mb-1" id="modalBookTitleText">Book Title</h3>
        <p class="text-gray-600 text-sm sm:text-base mb-3" id="modalBookAuthor">Author Name</p>

        <p class="text-sm sm:text-base md:text-[15px] text-gray-700 leading-relaxed mb-4" id="modalBookDescription">
          Description of the book goes here.
        </p>

        <p class="font-semibold text-[#C0392B] mb-2 text-base sm:text-lg">
          Rp <span id="modalBookPrice">0</span>
        </p>

        <p class="text-sm sm:text-base text-gray-500 mb-5">
          Stock: <span id="modalBookStock" class="font-medium text-[#1B3C53]">0</span>
        </p>

        {{-- Buttons --}}
        <div class="flex flex-wrap gap-3">
          <button id="modalAddToCart"
            class="bg-[#1B3C53] text-white text-sm sm:text-base font-medium px-4 sm:px-6 py-2 sm:py-2.5 rounded-md hover:bg-[#102a3e] transition">
            🛒 Add to Cart
          </button>
        </div>

        {{-- Out of Stock Notice --}}
        <p id="modalOutOfStock"
          class="hidden mt-4 text-sm sm:text-base text-red-600 bg-red-50 border border-red-200 rounded-md px-4 py-2">
          ❌ This book is currently out of stock.
        </p>
      </div>
    </div>
  </div>
</div>

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      const cartAlert = document.getElementById('cartAlert');
      const alertBox = document.getElementById('cartAlertBox');
      const alertText = document.getElementById('cartAlertText');
      const closeBtn = document.getElementById('closeAlertBtn');

      /* === ALERT === */
      function showAlert(message, type = 'success') {
        const isSuccess = type === 'success';
        alertText.textContent = message;
        alertBox.classList.remove('bg-red-50', 'border-red-300', 'text-red-700');
        alertBox.classList.remove('bg-[#F9F3EF]', 'border-[#d2c1b6]/70', 'text-[#1B3C53]');
        if (isSuccess) {
          alertBox.classList.add('bg-[#F9F3EF]', 'border-[#d2c1b6]/70', 'text-[#1B3C53]');
        } else {
          alertBox.classList.add('bg-red-50', 'border-red-300', 'text-red-700');
        }
        cartAlert.classList.remove('hidden');
        cartAlert.classList.add('flex');
        setTimeout(() => {
          alertBox.classList.add('opacity-100', 'scale-100');
          alertBox.classList.remove('opacity-0', 'scale-95');
        }, 10);
        setTimeout(() => hideAlert(), 2500);
      }

      function hideAlert() {
        alertBox.classList.remove('opacity-100', 'scale-100');
        alertBox.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
          cartAlert.classList.add('hidden');
          cartAlert.classList.remove('flex');
        }, 300);
      }
      closeBtn.addEventListener('click', hideAlert);

      function updateCartCount(count) {
        document.querySelectorAll('.cart-count').forEach(el => {
          el.textContent = count;
          el.classList.add('animate-bounce');
          setTimeout(() => el.classList.remove('animate-bounce'), 500);
        });
      }

      /* === ADD TO CART === */
      document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function () {
          const bookId = Number(this.dataset.bookId);
          const title = this.dataset.title;
          const price = Number(this.dataset.price);
          const image = this.dataset.image;

          if (!isLoggedIn) {
            const stock = Number(this.dataset.stock) || 0;
            const cart = JSON.parse(localStorage.getItem('guest_cart') || '[]');
            const existing = cart.find(i => i.book_id === bookId);

            if (existing) {
              if (existing.stock > 0 && existing.quantity < existing.stock) {
                existing.quantity += 1;
                showAlert(`"${title}" added to your cart!`, 'success');
              } else {
                showAlert(`"${title}" is at maximum available stock (${existing.stock}).`, 'error');
              }
            } else {
              cart.push({ book_id: bookId, title, price, quantity: 1, image, stock });
              showAlert(`"${title}" added to your cart!`, 'success');
            }

            localStorage.setItem('guest_cart', JSON.stringify(cart));
            const totalCount = cart.reduce((sum, i) => sum + i.quantity, 0);
            window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: totalCount } }));
            return;
          }

          // === Logged in: send to backend ===
          addToUserCart(bookId, title, this);
        });
      });

      async function addToUserCart(bookId, title, button) {
        button.disabled = true;
        const original = button.innerHTML;
        button.innerHTML = '⏳';
        try {
          const res = await fetch(`/user/cart/${bookId}`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ quantity: 1 })
          });
          const data = await res.json();
          if (res.ok && data.success) {
            showAlert(`"${title}" added to your cart!`);
            updateCartCount(data.cart_count);
          } else {
            throw new Error(data.message || 'Failed to add to cart');
          }
        } catch (err) {
          console.error(err);
          showAlert('Something went wrong. Please try again.', 'error');
        } finally {
          button.disabled = false;
          button.innerHTML = original;
        }
      }

      /* === Book Modal === */
      function openBookModal(button) {
        const modal = document.getElementById('bookModal');
        const modalBox = document.getElementById('bookModalBox');
        const addToCartBtn = document.getElementById('modalAddToCart');
        const outOfStockMsg = document.getElementById('modalOutOfStock');

        document.getElementById('modalBookTitle').textContent = button.dataset.title;
        document.getElementById('modalBookTitleText').textContent = button.dataset.title;
        document.getElementById('modalBookAuthor').textContent = button.dataset.author || 'Unknown Author';
        document.getElementById('modalBookDescription').textContent = button.dataset.description || 'No description available.';
        document.getElementById('modalBookPrice').textContent = button.dataset.price;
        document.getElementById('modalBookStock').textContent = button.dataset.stock;
        document.getElementById('modalBookImage').src = button.dataset.image;

        const stock = parseInt(button.dataset.stock || 0);

        if (stock <= 0) {
          addToCartBtn.disabled = true;
          addToCartBtn.classList.add('opacity-50', 'cursor-not-allowed');
          addToCartBtn.innerHTML = 'Out of Stock';
          outOfStockMsg.classList.remove('hidden');
        } else {
          addToCartBtn.disabled = false;
          addToCartBtn.classList.remove('opacity-50', 'cursor-not-allowed');
          addToCartBtn.innerHTML = '🛒 Add to Cart';
          outOfStockMsg.classList.add('hidden');
        }

        modal.classList.remove('hidden');
        setTimeout(() => {
          modal.classList.add('flex');
          modalBox.classList.add('opacity-100', 'scale-100');
          modalBox.classList.remove('opacity-0', 'scale-95');
        }, 10);
      }

      function closeBookModal() {
        const modal = document.getElementById('bookModal');
        const modalBox = document.getElementById('bookModalBox');
        modalBox.classList.remove('opacity-100', 'scale-100');
        modalBox.classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
          modal.classList.add('hidden');
          modal.classList.remove('flex');
        }, 200);
      }
      document.getElementById('bookModal').addEventListener('click', function (e) {
        if (e.target === this) closeBookModal();
      });

      window.openBookModal = openBookModal;
      window.closeBookModal = closeBookModal;
    });
  </script>
@endpush