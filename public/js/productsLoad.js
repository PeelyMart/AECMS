console.log("loaded javascript");

loadProducts();



async function loadProducts() {
  const res = await fetch('../endpoint/productsRetrieve.php');
  const products = await res.json();

  const tbody = document.getElementById('productsBody');
  tbody.innerHTML = "";

  products.forEach(product => {
    const row = document.createElement("tr");

    row.innerHTML = `
      <td>${product.id}</td>
      <td>${product.name}</td>
      <td>${product.l_sku}</td>
      <td>${product.s_sku}</td>
      <td>${product.t_sku}</td>
      <td>${product.qty}</td>
      <td>₱${parseFloat(product.unit_price).toFixed(2)}</td>

      <td class="actionsCell">
        <a href="a-viewProd.html?id=${product.id}" class="viewButton">View</a>
        <a href="../endpoint/deleteProduct.php?id=${product.id}" class="deleteButton"">Delete</a>
      </td>
    `;

    tbody.appendChild(row);
  });

  attachDeleteEvents();
}
