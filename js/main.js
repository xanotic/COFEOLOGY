document.addEventListener("DOMContentLoaded", () => {
  // Mobile Menu Toggle
  const mobileMenuToggle = document.querySelector(".mobile-menu-toggle")
  if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener("click", () => {
      document.body.classList.toggle("mobile-menu-active")
    })
  }

  // Cart Functionality
  initializeCart()
  updateCartCount()

  // Menu Filter
  const filterButtons = document.querySelectorAll(".menu-filter-btn")
  if (filterButtons.length > 0) {
    filterButtons.forEach((button) => {
      button.addEventListener("click", function () {
        const category = this.getAttribute("data-category")

        // Update active button
        filterButtons.forEach((btn) => btn.classList.remove("active"))
        this.classList.add("active")

        // Filter menu items
        filterMenuItems(category)
      })
    })
  }
})

// Cart Functions
function initializeCart() {
  // Initialize cart in localStorage if it doesn't exist
  if (!localStorage.getItem("cart")) {
    localStorage.setItem("cart", JSON.stringify([]))
  }
}

function getCart() {
  return JSON.parse(localStorage.getItem("cart")) || []
}

function saveCart(cart) {
  localStorage.setItem("cart", JSON.stringify(cart))
  updateCartCount()
}

function updateCartCount() {
  const cartCount = document.getElementById("cart-count")
  if (cartCount) {
    const cart = getCart()
    const count = cart.reduce((total, item) => total + item.quantity, 0)
    cartCount.textContent = count
  }
}

function addToCart(itemId, name, price, image, quantity = 1) {
  const cart = getCart()
  const existingItem = cart.find((item) => item.id === itemId)

  if (existingItem) {
    existingItem.quantity += quantity
  } else {
    cart.push({
      id: itemId,
      name: name,
      price: price,
      image: image,
      quantity: quantity,
    })
  }

  saveCart(cart)

  // Show notification
  showNotification(`${name} added to cart!`)
}

function removeFromCart(itemId) {
  const cart = getCart()
  const updatedCart = cart.filter((item) => item.id !== itemId)
  saveCart(updatedCart)
}

function updateCartItemQuantity(itemId, quantity) {
  const cart = getCart()
  const item = cart.find((item) => item.id === itemId)

  if (item) {
    item.quantity = quantity
    if (item.quantity <= 0) {
      removeFromCart(itemId)
    } else {
      saveCart(cart)
    }
  }
}

function clearCart() {
  localStorage.setItem("cart", JSON.stringify([]))
  updateCartCount()
}

function calculateCartTotal() {
  const cart = getCart()
  return cart.reduce((total, item) => total + item.price * item.quantity, 0)
}

// Menu Functions
function filterMenuItems(category) {
  const menuItems = document.querySelectorAll(".menu-item")

  menuItems.forEach((item) => {
    if (category === "all" || item.getAttribute("data-category") === category) {
      item.style.display = "block"
    } else {
      item.style.display = "none"
    }
  })
}

// Fetch Functions
async function fetchPopularItems() {
  try {
    const response = await fetch("api/popular-items.php")
    const data = await response.json()

    if (data.success) {
      renderPopularItems(data.items)
    } else {
      throw new Error(data.message || "Failed to fetch popular items")
    }
  } catch (error) {
    console.error("Error fetching popular items:", error)
    document.getElementById("popular-items-container").innerHTML = `
            <div class="error-message">
                <p>Failed to load popular items. Please try again later.</p>
            </div>
        `
  }
}

function renderPopularItems(items) {
  const container = document.getElementById("popular-items-container")

  if (!container) return

  if (items.length === 0) {
    container.innerHTML = "<p>No popular items found.</p>"
    return
  }

  let html = ""

  items.forEach((item) => {
    html += `
            <div class="menu-item" data-category="${item.category}">
                <div class="menu-item-image">
                    <img src="${item.image || "images/placeholder.jpg"}" alt="${item.name}">
                </div>
                <div class="menu-item-content">
                    <div class="menu-item-title">
                        <h3>${item.name}</h3>
                        <div class="menu-item-price">${formatCurrency(item.price)}</div>
                    </div>
                    <div class="menu-item-description">
                        ${item.description}
                    </div>
                    <div class="menu-item-actions">
                        <button class="btn btn-primary btn-sm" onclick="addToCart(${item.id}, '${item.name}', ${item.price}, '${item.image || "images/placeholder.jpg"}')">
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        `
  })

  container.innerHTML = html
}

// Utility Functions
function formatCurrency(amount) {
  return "RM " + Number.parseFloat(amount).toFixed(2)
}

function showNotification(message, type = "success") {
  // Create notification element
  const notification = document.createElement("div")
  notification.className = `notification ${type}`
  notification.textContent = message

  // Add to document
  document.body.appendChild(notification)

  // Show notification
  setTimeout(() => {
    notification.classList.add("show")
  }, 10)

  // Remove after 3 seconds
  setTimeout(() => {
    notification.classList.remove("show")
    setTimeout(() => {
      document.body.removeChild(notification)
    }, 300)
  }, 3000)
}
