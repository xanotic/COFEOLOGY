// Mobile menu toggle
document.addEventListener("DOMContentLoaded", () => {
  const mobileMenuToggle = document.querySelector(".mobile-menu-toggle")
  const mainNav = document.querySelector(".main-nav")

  if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener("click", () => {
      mainNav.classList.toggle("active")
    })
  }

  // Category tabs
  const categoryTabs = document.querySelectorAll(".category-tab")

  categoryTabs.forEach((tab) => {
    tab.addEventListener("click", function () {
      // Remove active class from all tabs
      categoryTabs.forEach((t) => t.classList.remove("active"))

      // Add active class to clicked tab
      this.classList.add("active")

      // Hide all menu items
      const menuItems = document.querySelectorAll(".menu-items")
      menuItems.forEach((item) => (item.style.display = "none"))

      // Show selected category items
      const categoryId = this.getAttribute("data-category")
      document.getElementById("category-" + categoryId).style.display = "grid"
    })
  })
})

// Cart functionality
let cart = []
const cartItems = document.getElementById("cartItems")
const cartTotal = document.getElementById("cartTotal")
const cartCount = document.getElementById("cartCount")
const cartSidebar = document.getElementById("cartSidebar")
const cartOverlay = document.getElementById("cartOverlay")

// Add item to cart
function addToCart(id, name, price) {
  // Check if item already exists in cart
  const existingItem = cart.find((item) => item.id === id)

  if (existingItem) {
    existingItem.quantity++
  } else {
    cart.push({
      id: id,
      name: name,
      price: price,
      quantity: 1,
    })
  }

  updateCart()
  toggleCart(true)
}

// Update cart display
function updateCart() {
  // Clear cart items
  cartItems.innerHTML = ""

  // Add each item to cart
  cart.forEach((item) => {
    const cartItem = document.createElement("div")
    cartItem.className = "cart-item"
    cartItem.innerHTML = `
            <div class="cart-item-info">
                <div class="cart-item-name">${item.name}</div>
                <div class="cart-item-price">RM ${item.price.toFixed(2)}</div>
            </div>
            <div class="cart-item-quantity">
                <button class="quantity-btn" onclick="updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                <input type="text" class="quantity-input" value="${item.quantity}" readonly>
                <button class="quantity-btn" onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                <span class="remove-item" onclick="removeItem(${item.id})">🗑️</span>
            </div>
        `
    cartItems.appendChild(cartItem)
  })

  // Update total
  const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0)
  cartTotal.textContent = `RM ${total.toFixed(2)}`

  // Update cart count
  const count = cart.reduce((sum, item) => sum + item.quantity, 0)
  cartCount.textContent = count

  // Save cart to localStorage
  localStorage.setItem("cart", JSON.stringify(cart))
}

// Update item quantity
function updateQuantity(id, quantity) {
  if (quantity <= 0) {
    removeItem(id)
    return
  }

  const item = cart.find((item) => item.id === id)
  if (item) {
    item.quantity = quantity
    updateCart()
  }
}

// Remove item from cart
function removeItem(id) {
  cart = cart.filter((item) => item.id !== id)
  updateCart()
}

// Toggle cart sidebar
function toggleCart(show = null) {
  if (show === true || (show === null && !cartSidebar.classList.contains("active"))) {
    cartSidebar.classList.add("active")
    cartOverlay.classList.add("active")
  } else {
    cartSidebar.classList.remove("active")
    cartOverlay.classList.remove("active")
  }
}

// Proceed to checkout
function proceedToCheckout() {
  if (cart.length === 0) {
    alert("Your cart is empty!")
    return
  }

  // Redirect to checkout page
  window.location.href = "checkout.php"
}

// Load cart from localStorage on page load
document.addEventListener("DOMContentLoaded", () => {
  const savedCart = localStorage.getItem("cart")
  if (savedCart) {
    cart = JSON.parse(savedCart)
    updateCart()
  }
})
