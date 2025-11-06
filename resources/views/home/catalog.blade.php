<section id="ourCollection" class="container mx-auto px-6 mb-16">
  <h3 class="text-center text-2xl font-bold text-[#1B3C53] mb-3">Our Collection</h3>
  <p class="text-center text-gray-600 mb-8">Find a wide selection of interesting books only at LiteraMarket.</p>

  <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
    @forelse ($books as $book)
      <div
        class="border border-gray-300 bg-[#f9f9f9] flex flex-col items-center shadow-sm rounded-md hover:shadow-md transition-all">
        <div class="w-full h-120 border-b border-gray-300 flex justify-center items-center bg-white rounded-t-md">
          <img src="{{ $book->image ?? asset('images/default-book.jpg') }}" alt="{{ $book->title }}"
            class="h-full object-contain">
        </div>

        <div class="w-full px-3 py-3 flex flex-col justify-between flex-grow text-left">
          <div>
            <h4 class="text-sm font-bold text-gray-800 truncate">{{ $book->title }}</h4>
            <p class="text-sm text-gray-600">{{ $book->author }}</p>
          </div>

          <div class="flex justify-between items-center mt-3">
            <div class="flex gap-1">
              {{-- Detail Button --}}
              <button class="bg-[#002D72] text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-[#001E4D]"
                data-book-id="{{ $book->id }}" data-title="{{ $book->title }}" data-author="{{ $book->author }}"
                data-description="{{ $book->description }}" data-price="{{ $book->price ?? 0 }}"
                data-stock="{{ $book->stock }}" data-image="{{ $book->image ?? asset('images/default-book.jpg') }}"
                onclick="openBookModal(this)">
                Detail
              </button>

              {{-- Add to Cart Button --}}
              <button
                class="add-to-cart bg-[#1B3C53] text-white text-sm font-medium px-3 py-2 rounded-md hover:bg-[#102a3e] transition disabled:opacity-50 disabled:cursor-not-allowed"
                data-book-id="{{ $book->id }}" data-title="{{ $book->title }}" data-price="{{ $book->price ?? 0 }}"
                data-image="{{ $book->image ?? asset('images/default-book.jpg') }}">
                🛒
              </button>
            </div>

            <span class="text-[#C0392B] font-semibold text-sm">
              Rp {{ number_format($book->price ?? 51000, 0, ',', '.') }}
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
<div id="bookModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
  <div
    class="bg-white rounded-xl shadow-2xl w-[90%] max-w-3xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300"
    id="bookModalBox">
    <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4 bg-[#F9F3EF]">
      <h2 class="text-lg font-semibold text-[#1B3C53]" id="modalBookTitle">Book Title</h2>
      <button onclick="closeBookModal()" class="text-[#1B3C53]/70 hover:text-[#1B3C53] text-lg font-bold">✕</button>
    </div>
    <div class="grid md:grid-cols-2 gap-6 p-6 items-center">
      <div class="flex justify-center">
        <img id="modalBookImage" src="{{ asset('images/default-book.jpg') }}" alt="Book Image"
          class="h-64 object-contain rounded-md">
      </div>
      <div>
        <h3 class="font-bold text-[#1B3C53] text-xl mb-1" id="modalBookTitleText">Book Title</h3>
        <p class="text-gray-600 text-sm mb-3" id="modalBookAuthor">Author Name</p>
        <p class="text-sm text-gray-700 mb-4" id="modalBookDescription">Description of the book goes here.</p>
        <p class="font-semibold text-[#C0392B] mb-2">
          Rp <span id="modalBookPrice">0</span>
        </p>
        <p class="text-sm text-gray-500 mb-4">
          Stock: <span id="modalBookStock">0</span>
        </p>
        <div class="flex gap-3">
          <button id="modalAddToCart"
            class="bg-[#1B3C53] text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-[#102a3e] transition">
            🛒 Add to Cart
          </button>
        </div>
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
            // === Guest: save to localStorage ===
            const cart = JSON.parse(localStorage.getItem('guest_cart') || '[]');
            const existing = cart.find(i => i.book_id === bookId);
            if (existing) {
              existing.quantity += 1;
            } else {
              cart.push({ book_id: bookId, title, price, quantity: 1, image });
            }
            localStorage.setItem('guest_cart', JSON.stringify(cart));
            const totalCount = cart.reduce((sum, i) => sum + i.quantity, 0);
            window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: totalCount } }));
            showAlert(`"${title}" added to your cart!`, 'success');
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
        document.getElementById('modalBookTitle').textContent = button.dataset.title;
        document.getElementById('modalBookTitleText').textContent = button.dataset.title;
        document.getElementById('modalBookAuthor').textContent = button.dataset.author || 'Unknown Author';
        document.getElementById('modalBookDescription').textContent = button.dataset.description || 'No description available.';
        document.getElementById('modalBookPrice').textContent = button.dataset.price;
        document.getElementById('modalBookStock').textContent = button.dataset.stock;
        document.getElementById('modalBookImage').src = button.dataset.image;

        modal.classList.remove('hidden');
        setTimeout(() => {
          modal.classList.add('flex');
          modalBox.classList.add('opacity-100', 'scale-100');
          modalBox.classList.remove('opacity-0', 'scale-95');
        }, 10);

        const addToCartBtn = document.getElementById('modalAddToCart');
        addToCartBtn.onclick = () => {
          document.querySelector(`[data-book-id="${button.dataset.bookId}"].add-to-cart`).click();
          closeBookModal();
        };
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