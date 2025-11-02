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
              <a href="#" class="bg-[#002D72] text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-[#001E4D]">
                Detail
              </a>

              {{-- Add to Cart Button --}}
              <button
                class="add-to-cart bg-[#1B3C53] text-white text-sm font-medium px-3 py-2 rounded-md hover:bg-[#102a3e] transition disabled:opacity-50 disabled:cursor-not-allowed"
                data-book-id="{{ $book->id }}" data-title="{{ $book->title }}">
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

    {{-- Header --}}
    <div class="border-b border-[#d2c1b6]/60 px-4 py-3 flex justify-between items-center rounded-t-xl">
      <h2 class="font-semibold text-[#1B3C53]">LiteraMarket</h2>
      <button id="closeAlertBtn" class="text-[#1B3C53]/60 hover:text-[#1B3C53] text-sm font-bold">✕</button>
    </div>

    {{-- Content --}}
    <div class="px-5 py-4 text-start">
      <p id="cartAlertText" class="text-[#1B3C53] text-sm font-medium"></p>
    </div>
  </div>
</div>

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const cartAlert = document.getElementById('cartAlert');
      const alertBox = document.getElementById('cartAlertBox');
      const alertText = document.getElementById('cartAlertText');
      const closeBtn = document.getElementById('closeAlertBtn');
      const cartCountElement = document.querySelector('.cart-count');
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

      function showAlert(message, type = 'success') {
        const isSuccess = type === 'success';

        // Set message
        alertText.textContent = message;

        // Reset styles
        alertBox.classList.remove('bg-red-50', 'border-red-300', 'text-red-700');
        alertBox.classList.remove('bg-[#F9F3EF]', 'border-[#d2c1b6]/70', 'text-[#1B3C53]');

        // Apply colors
        if (isSuccess) {
          alertBox.classList.add('bg-[#F9F3EF]', 'border-[#d2c1b6]/70', 'text-[#1B3C53]');
        } else {
          alertBox.classList.add('bg-red-50', 'border-red-300', 'text-red-700');
        }

        // Show modal
        cartAlert.classList.remove('hidden');
        cartAlert.classList.add('flex');
        setTimeout(() => {
          alertBox.classList.add('opacity-100', 'scale-100');
          alertBox.classList.remove('opacity-0', 'scale-95');
        }, 10);

        // Auto close
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
        document.querySelectorAll('.cart-count').forEach((el) => {
          el.textContent = count;
          el.classList.add('animate-bounce');
          setTimeout(() => el.classList.remove('animate-bounce'), 500);
        });
      }

      async function addToCart(bookId, bookTitle, button) {
        button.disabled = true;
        const originalText = button.innerHTML;
        button.innerHTML = '⏳';

        try {
          const response = await fetch(`/user/cart/${bookId}`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ quantity: 1 }),
          });

          const data = await response.json();

          if (response.ok && data.success) {
            showAlert(`"${bookTitle}" added to your cart!`, 'success');
            updateCartCount(data.cart_count);
          } else {
            throw new Error(data.message || 'Failed to add to cart');
          }
        } catch (error) {
          console.error('❌ Add to cart error:', error);
          showAlert('Something went wrong. Please try again.', 'error');
        } finally {
          button.disabled = false;
          button.innerHTML = originalText;
        }
      }

      document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function () {
          const bookId = this.dataset.bookId;
          const bookTitle = this.dataset.title;
          if (!bookId) return showAlert('Invalid book. Please refresh the page.', 'error');
          addToCart(bookId, bookTitle, this);
        });
      });
    });
  </script>
@endpush