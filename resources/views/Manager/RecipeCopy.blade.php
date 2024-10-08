@extends('Components.Manager')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let branchName = document.getElementById('branch_name').value;
        let titleElement = document.getElementById('dynamic-title');
        titleElement.textContent = branchName + ' | Manager - Recipe';
    });
</script>
@push('styles')
    <link rel="stylesheet" href="{{ asset('CSS/Manager/recipe.css') }}">
@endpush

@section('main')
    <main id="recipePage">
        @if (session('success'))
            <div id="success" class="alert alert-success">
                {{ session('success') }}
            </div>
            <script>
                setTimeout(() => {
                    document.getElementById('success').classList.add('alert-hide');
                }, 2000);

                setTimeout(() => {
                    document.getElementById('success').style.display = 'none';
                }, 3000);
            </script>
        @endif

        @if (session('error'))
            <div id="error" class="alert alert-danger">
                {{ session('error') }}
            </div>
            <script>
                setTimeout(() => {
                    document.getElementById('error').classList.add('alert-hide');
                }, 2000);

                setTimeout(() => {
                    document.getElementById('error').style.display = 'none';
                }, 3000);
            </script>
        @endif

        @php
            $recipes = $recipes;
            $categories = $categories;
            $Stocks = $stocks;
            $category_id;
            $branch_id = $branch_id;
            $user_id = $user_id;
        @endphp

        <div class="productCategory">
            @foreach ($categories as $category)
                @php
                    $category_id = $category->id;
                @endphp

                <div class="categorydiv" id="showProductsInCategory"
                    onclick="window.location='{{ route('showCategoryProducts', [$category->id, $branch_id, $user_id]) }}'">
                    <div class="categoryImg">
                        <img src="{{ asset('Images/CategoryImages/' . $category->categoryImage) }}" alt="Category Image">
                    </div>
                    <div class="categoryDetails">
                        <h3>{{ $category->categoryName }}</h3>
                    </div>
                </div>
            @endforeach
        </div>

        @if (session('categoryProducts'))
            <div id="categoryProductOverlay" style="display: block;"></div>
            <div id="categoryProducts" style="display: flex;">
                <div class="table">
                    <table id="productRecipeTable">
                        <thead>
                            <tr>
                                <th>Product Category</th>
                                <th>Product Name</th>
                                <th>Product Variation</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $category_products = session('categoryProducts');
                            @endphp
                            @foreach ($category_products as $product)
                                <tr>
                                    <td>{{ $product->category_name }}</td>
                                    <td>{{ $product->productName }}</td>
                                    <td>{{ $product->productVariation }}</td>
                                    <td>
                                        <a id="add_recipe" href="#"><i id="add_recipe_i"
                                                onclick="addRecipe({{ json_encode($product) }}, {{ json_encode($recipes) }}, {{ json_encode($Stocks) }})"
                                                class="bx bx-list-plus"></i>
                                        </a>
                                        <a
                                            href="{{ route('viewProductRecipe', [$product->category_id, $product->id, $user_id, $branch_id]) }}"><i
                                                class="bx bx-show"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="btns">
                    <a href="{{ route('viewRecipePage', [$user_id, $branch_id]) }}"><button type="button"
                            onclick="closeProductCategory()">Close</button></a>
                </div>

            </div>
        @endif

        @if (session('productRecipe'))
            <div id="productRecipeOverlay" style="display: block;"></div>
            <div id="productRecipe" style="display: flex;">
                <p id="productRecipeitems"></p>
                <div class="table">
                    <table id="recipeTable">
                        <thead>
                            <tr>
                                <th>Recipe Item</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $productRecipe = session('productRecipe');
                            @endphp
                            @foreach ($productRecipe as $recipe)
                                <tr>
                                    <td>{{ $recipe->stock->itemName }}</td>
                                    <td>{{ $recipe->quantity }}</td>
                                    <td>
                                        <a
                                            href="{{ route('deleteStockFromRecipe', [$recipe->id, $recipe->category_id, $recipe->product_id]) }}">
                                            <i class='bx bxs-trash-alt'></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="btns">
                    <button type="button"
                        onclick="closeProductRecipe('{{ route('showCategoryProducts', [$productRecipe->category_id, $branch_id, $user_id]) }}')">
                        Close</button>
                </div>
            </div>
        @endif

        <div id="recipeOverlay"></div>
        <div class="recipePopup" id="recipePopup">
            <form id="recipepad" action="{{ route('createRecipe') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <h5 id="prod_name"></h5>
                <input type="hidden" name="user_id" value="{{ $user_id }}">
                <input type="hidden" name="branch_id" value="{{ $branch_id }}">
                <input type="hidden" name="cId" id="cID">
                <input type="hidden" name="pId" id="pID">
                <p id="recipeContainer" class="recipeContainer" name="productRecipe"></p>
                <textarea id="recipeTextArea" name="recipeItems" style="display: none;"></textarea>
                <div id="buttons">
                    <button type="button" onclick="closeRecipe()">Close</button>
                    <input type="submit" value="Add Recipe">
                </div>

            </form>
            <div id="recipelist">
                <div class="searchBar">
                    <input type="text" id="Search" placeholder="Search Item in Stock">
                    <i class='bx bx-search'></i>
                </div>
                <div class="stockContainer">
                    @foreach ($stocks as $stock)
                        <p id="{{ $stock->id }}" data-id="{{ $stock->id }}"
                            onclick="handleStockItemClick({{ json_encode($stock) }})">
                            {{ $stock->itemName }}</p>
                    @endforeach
                </div>
            </div>
        </div>

        <div id="addProductRecipeOverlay"></div>
        <div id="addProductRecipe">
            <h3>Add Recipe to Product</h3>
            <hr>

            <div class="inputdivs inputProductName" style="display: flex; margin:5px;">
                <div class="stockquantity">
                    <label for="item-name">Item Name</label>
                    <input type="text" id="item-name" placeholder="item Name" name="item-name" readonly required>
                </div>

                <div class="stockquantity">
                    <label for="item-stock-quantity">Item Stock Quantity</label>
                    <input type="text" id="item-stock-quantity" placeholder="123kg.." name="item-stock-quantity"
                        readonly required>
                </div>
            </div>

            <div class="inputdivs inputProductName" style="display: flex; margin:5px;">
                <div class="stockquantity">
                    <label for="item-quantity">Item Quantity</label>
                    <input type="number" id="item-quantity" min="0" placeholder="Convert to base unit"
                        name="itemQuantity" step="any" min="0" required>
                </div>

                <div class="unitselection">
                    <label for="iQUnit">Base Unit</label>
                    <input type="text" id="iQUnit" name="unit1" readonly>
                </div>
            </div>

            <div class="btns">
                <input type="submit" value="Add" id="addItemButton">
                <button type="button" id="cancel" onclick="closeHandleStockItemClick()">close</button>
            </div>

        </div>

        <div id="editProductRecipeOverlay"></div>
        <form id="editProductRecipe" action="{{ route('editProductRecipe') }}" method="POST"
            enctype="multipart/form-data" onsubmit="handleFormSubmit(event)">

            @csrf
            <h3>Edit Recipe</h3>
            <hr>
            <input type="hidden" id="recipeid" name="recipeId">
            <div class="inputdivs inputProductName" style="display: flex; margin:5px;">
                <div class="stockquantity">
                    <label for="item-name">Item Name</label>
                    <input type="text" id="edit-item-name" placeholder="item Name" name="item-name" readonly
                        required>
                </div>

                <div class="stockquantity">
                    <label for="item-stock-quantity">Item Stock Quantity</label>
                    <input type="text" id="edit-item-stock-quantity" placeholder="123kg.." name="item-stock-quantity"
                        required>
                </div>

                <div class="unitselection">
                    <label for="editQUnit">Unit</label>
                    <select name="unit1" id="editQUnit">
                        <option value="" selected disabled>Select unit</option>
                        <option value="mg">Milligram</option>
                        <option value="g">Gram</option>
                        <option value="kg">Kilogram</option>
                        <option value="ml">Milliliter</option>
                        <option value="liter">Liter</option>
                        <option value="gal">Gallon</option>
                        <option value="lbs">Pound</option>
                        <option value="oz">Ounce</option>
                        <option value="dozen">Dozen</option>
                        <option value="piece">Piece</option>
                    </select>
                </div>
            </div>

            <div class="btns">
                <input type="submit" value="Update">
                <button type="button" id="cancel" onclick="closeEditProductRecipe()">close</button>
            </div>
        </form>
    </main>

    <script>
        let product_name;

        function closeProductCategory() {
            let overlay = document.getElementById('categoryProductOverlay');
            let popup = document.getElementById('categoryProducts');
            overlay.style.display = 'none';
            popup.style.display = 'none';

        }

        // function showProductRecipe() {
        //     const overlay = document.getElementById('productRecipeOverlay');
        //     const popup = document.getElementById('productRecipe');

        //     overlay.style.display = 'block';
        //     popup.style.display = 'flex';
        // }

        function closeProductRecipe(route) {
            let overlay = document.getElementById('productRecipeOverlay');
            let popup = document.getElementById('productRecipe');

            overlay.style.display = 'none';
            popup.style.display = 'none';

            window.location.href = route;
        }

        function addRecipe(product, recipes, stocks) {
            let overlay = document.getElementById('recipeOverlay');
            let popup = document.getElementById('recipePopup');
            let pId = document.getElementById('pID');
            let cID = document.getElementById('cID');
            let form = document.getElementById('recipepad');
            let pname = document.getElementById('prod_name');
            let recipeTextArea = document.getElementById('recipeTextArea');
            let recipeContainer = document.getElementById('recipeContainer');

            recipeTextArea.value = '';

            pname.textContent = product.productVariation + " " + product.productName + " Recipe";
            pId.value = product.id;
            cID.value = product.category_id;

            overlay.style.display = 'block';
            popup.style.display = 'flex';
            while (recipeContainer.firstChild) {
                recipeContainer.removeChild(recipeContainer.firstChild);
            }

            recipes.forEach(recipe => {
                if (product.id === recipe.product_id) {
                    const match = recipe.quantity.match(/^([\d.]+)\s*(.*)$/);

                    const quantity = match[1];
                    const Unit = match[2];
                    const itemName = recipe.stock.itemName;
                    const id = recipe.stock_id;

                    document.getElementById(id).style.display = 'none';

                    const Recipediv = document.createElement('div');
                    Recipediv.style.display = 'flex';
                    Recipediv.style.alignItems = 'center';

                    const existingRecipeItem = document.createElement('p');
                    existingRecipeItem.classList.add('recipe-item');
                    existingRecipeItem.style.margin = '2px';
                    existingRecipeItem.style.width = '95%';
                    existingRecipeItem.textContent = `${recipe.quantity} ${itemName}`;
                    existingRecipeItem.dataset.id = id;
                    existingRecipeItem.dataset.quantity = quantity;

                    const recipeEditBtn = document.createElement('i');
                    recipeEditBtn.classList.add('bx', 'bx-edit-alt', 'edit-item');
                    recipeEditBtn.style.fontSize = '1vw';
                    recipeEditBtn.style.padding = '5px';
                    recipeEditBtn.style.borderRadius = '50%';
                    recipeEditBtn.style.marginRight = '5px'

                    const recipeRemoveBtn = document.createElement('i');
                    recipeRemoveBtn.classList.add('bx', 'bx-x', 'remove-item');
                    recipeRemoveBtn.style.fontSize = '1.2vw';
                    recipeRemoveBtn.style.borderRadius = '50%';
                    recipeRemoveBtn.style.marginRight = '5px';

                    Recipediv.appendChild(existingRecipeItem);
                    Recipediv.appendChild(recipeEditBtn);
                    Recipediv.appendChild(recipeRemoveBtn);
                    recipeContainer.appendChild(Recipediv);

                    recipeEditBtn.addEventListener('click', function() {
                        updateproductRecipe(itemName, quantity, Unit, recipe.id);
                    });

                    recipeRemoveBtn.addEventListener('click', function() {
                        Recipediv.remove();
                        const itemToRemove = `${quantity} ${Unit}~${id}`;
                        const regex = new RegExp(`\\b${itemToRemove},?\\s?`, 'g');
                        recipeTextArea.value = recipeTextArea.value.replace(regex, '');
                    });

                    if (recipeTextArea.value === '') {
                        recipeTextArea.value = `${quantity} ${Unit}~${id}`;
                    } else {
                        recipeTextArea.value += `, ${quantity} ${Unit}~${id}`;
                    }
                }
            });
        }

        function updateproductRecipe(itemName, quantity, Unit, id) {
            let overlay = document.getElementById('editProductRecipeOverlay');
            let popup = document.getElementById('editProductRecipe');

            const recipePopup = document.getElementById('recipePopup');
            const categoryProducts = document.getElementById('categoryProducts');

            document.getElementById('edit-item-name').value = itemName;
            document.getElementById('edit-item-stock-quantity').value = quantity;
            document.getElementById('editQUnit').value = Unit;
            document.getElementById('recipeid').value = id;

            recipePopup.style.display = 'none';
            categoryProducts.style.display = 'none';

            overlay.style.display = 'block';
            popup.style.display = 'flex';
        }

        function closeEditProductRecipe() {
            let overlay = document.getElementById('editProductRecipeOverlay');
            let popup = document.getElementById('editProductRecipe');
            const recipePopup = document.getElementById('recipePopup');
            const categoryProducts = document.getElementById('categoryProducts');

            recipePopup.style.display = 'flex';
            categoryProducts.style.display = 'flex';
            overlay.style.display = 'none';
            popup.style.display = 'none';
        }

        function closeRecipe() {
            let recipeContainer = document.querySelector('.recipeContainer');
            while (recipeContainer.firstChild) {
                recipeContainer.removeChild(recipeContainer.firstChild);
            }

            let overlay = document.getElementById('recipeOverlay');
            let popup = document.getElementById('recipePopup');

            overlay.style.display = 'none';
            popup.style.display = 'none';
        }

        function refreshStockContainer(stocks, stockItemIDsInRecipes) {
            let stockContainer = document.querySelector('.stockContainer');
            stockContainer.innerHTML = '';

            stocks.forEach(stock => {
                if (!stockItemIDsInRecipes.has(stock.id)) {
                    const stockItem = document.createElement('p');
                    stockItem.textContent = stock.itemName;
                    stockItem.dataset.id = stock.id;
                    stockItem.addEventListener('click', function() {
                        handleStockItemClick(stock);
                    });
                    stockContainer.appendChild(stockItem);
                }
            });
        }

        const recipeTextArea = document.getElementById('recipeTextArea');

        function addItemToRecipe(itemName, quantity, unit, id) {
            const recipeContainer = document.querySelector('#recipeContainer');
            const recipeTextArea = document.querySelector('#recipeTextArea');

            const existingItem = Array.from(recipeContainer.querySelectorAll('.recipe-item')).find(item => {
                return item.dataset.id === String(id) && item.dataset.unit === unit;
            });

            if (existingItem) {
                const currentQuantity = parseFloat(existingItem.dataset.quantity);
                const newQuantity = currentQuantity + parseFloat(quantity);

                existingItem.textContent = `${newQuantity.toFixed(2)} ${unit} ${itemName}`;
                existingItem.dataset.quantity = newQuantity.toFixed(2);

                const regex = new RegExp(`\\b${currentQuantity}\\s+${unit}~${id}\\b`);
                recipeTextArea.value = recipeTextArea.value.replace(regex, `${newQuantity.toFixed(2)} ${unit}~${id}`);
            } else {
                const newRecipeItem = document.createElement('div');
                newRecipeItem.style.display = 'flex';
                newRecipeItem.style.alignItems = 'center';

                const newRecipeContent = document.createElement('p');
                newRecipeContent.classList.add('recipe-item');
                newRecipeContent.style.margin = '2px';
                newRecipeContent.style.width = '90%';
                newRecipeContent.textContent = `${quantity} ${unit} ${itemName}`;
                newRecipeContent.dataset.id = id;
                newRecipeContent.dataset.quantity = quantity;
                newRecipeContent.dataset.unit = unit;

                const removeButton = document.createElement('i');
                removeButton.classList.add('bx', 'bx-x', 'remove-item');
                removeButton.style.fontSize = '1.2vw';
                removeButton.style.borderRadius = '50%';
                removeButton.style.marginRight = '5px';

                newRecipeItem.appendChild(newRecipeContent);
                newRecipeItem.appendChild(removeButton);
                recipeContainer.appendChild(newRecipeItem);

                removeButton.addEventListener('click', function() {
                    newRecipeItem.remove();
                    const regex = new RegExp(`\\b${quantity}\\s+${unit}~${id}\\b`);
                    recipeTextArea.value = recipeTextArea.value.replace(regex, '');
                });

                if (recipeTextArea.value === '') {
                    recipeTextArea.value = `${quantity} ${unit}~${id}`;
                } else {
                    recipeTextArea.value += `, ${quantity} ${unit}~${id}`;
                }
            }
        }

        function closeHandleStockItemClick() {
            const addProductRecipeOverlay = document.querySelector('#addProductRecipeOverlay');
            const addProductRecipe = document.querySelector('#addProductRecipe');
            const recipePopup = document.getElementById('recipePopup');
            const categoryProducts = document.getElementById('categoryProducts');

            addProductRecipeOverlay.style.display = 'none';
            addProductRecipe.style.display = 'none';
            recipePopup.style.display = 'flex';
            categoryProducts.style.display = 'flex';
        }

        function handleStockItemClick(stock) {
            const addProductRecipeOverlay = document.querySelector('#addProductRecipeOverlay');
            const addProductRecipe = document.querySelector('#addProductRecipe');
            const addItemButton = document.querySelector('#addItemButton');
            const cancelButton = document.querySelector('#cancel');
            let itemName = document.getElementById('item-name');
            let itemStockQuantity = document.getElementById('item-stock-quantity');
            let unitSelect = document.querySelector('#iQUnit');
            const recipePopup = document.getElementById('recipePopup');
            const categoryProducts = document.getElementById('categoryProducts');
            addProductRecipeOverlay.style.display = 'block';
            addProductRecipe.style.display = 'flex';

            let match = stock.itemQuantity.match(/^([\d\.]+)\s*(\S+)/);
            let value = parseFloat(match[1]);
            let unit = match[2];

            itemName.value = stock.itemName;
            itemStockQuantity.value = stock.itemQuantity;
            unitSelect.value = unit;

            recipePopup.style.display = 'none';
            categoryProducts.style.display = 'none';

            addItemButton.onclick = function() {
                const quantityInput = document.getElementById('item-quantity');
                const unitSelect = document.querySelector('#iQUnit');
                const enteredQuantity = parseFloat(quantityInput.value);
                const enteredUnit = unitSelect.value;

                const [stockQuantity, stockUnit] = parseQuantityAndUnit(stock.itemQuantity);
                const convertedQuantity = convertToStockUnit(enteredQuantity, enteredUnit, stockUnit);

                if (convertedQuantity > 0 && convertedQuantity <= stockQuantity) {
                    if (enteredQuantity && enteredUnit) {
                        addItemToRecipe(stock.itemName, enteredQuantity, enteredUnit, stock.id);

                        addProductRecipeOverlay.style.display = 'none';
                        addProductRecipe.style.display = 'none';
                        recipePopup.style.display = 'flex';
                        categoryProducts.style.display = 'flex';

                        quantityInput.value = '';
                        unitSelect.value = '';
                    } else {
                        alert("Please enter a valid quantity and select a unit.");
                    }
                } else {
                    alert('Quantity must be greater than 0 and less than or equal to the stock quantity.');
                }
            }

            cancelButton.onclick = function() {
                addProductRecipeOverlay.style.display = 'none';
                addProductRecipe.style.display = 'none';
                recipePopup.style.display = 'flex';
                categoryProducts.style.display = 'flex';
            };
        }

        function parseQuantityAndUnit(quantityWithUnit) {
            const regex = /^([0-9.]+)\s*([a-zA-Z]+)$/;
            const match = quantityWithUnit.match(regex);

            if (match) {
                const quantity = parseFloat(match[1]);
                const unit = match[2];
                return [quantity, unit];
            } else {
                throw new Error('Invalid quantity and unit format');
            }
        }

        function convertToStockUnit(quantity, fromUnit, toUnit) {
            const conversionRates = {
                'mg': {
                    'mg': 1,
                    'g': 0.001,
                    'kg': 0.000001,
                    'ml': 0.001,
                    'liter': 0.000001,
                    'gal': 0.000000264172,
                    'lbs': 0.00000220462,
                    'oz': 0.000035274
                },
                'g': {
                    'mg': 1000,
                    'g': 1,
                    'kg': 0.001,
                    'ml': 1,
                    'liter': 0.001,
                    'gal': 0.000264172,
                    'lbs': 0.00220462,
                    'oz': 0.035274
                },
                'kg': {
                    'mg': 1000000,
                    'g': 1000,
                    'kg': 1,
                    'ml': 1000,
                    'liter': 1,
                    'gal': 0.264172,
                    'lbs': 2.20462,
                    'oz': 35.274
                },
                'ml': {
                    'mg': 1,
                    'g': 0.001,
                    'kg': 0.000001,
                    'ml': 1,
                    'liter': 0.001,
                    'gal': 0.000264172,
                    'lbs': 0.00220462,
                    'oz': 0.035274
                },
                'liter': {
                    'mg': 1000000,
                    'g': 1000,
                    'kg': 1,
                    'ml': 1000,
                    'liter': 1,
                    'gal': 0.264172,
                    'lbs': 2.20462,
                    'oz': 35.274
                },
                'gal': {
                    'mg': 3785411.78,
                    'g': 3785.41,
                    'kg': 3.78541,
                    'ml': 3785.41,
                    'liter': 3.78541,
                    'gal': 1,
                    'lbs': 8.3454,
                    'oz': 128
                },
                'lbs': {
                    'mg': 453592.37,
                    'g': 453.592,
                    'kg': 0.453592,
                    'ml': 453.592,
                    'liter': 0.453592,
                    'gal': 0.119826,
                    'lbs': 1,
                    'oz': 16
                },
                'oz': {
                    'mg': 28349.5,
                    'g': 28.3495,
                    'kg': 0.0283495,
                    'ml': 28.3495,
                    'liter': 0.0283495,
                    'gal': 0.0078125,
                    'lbs': 0.0625,
                    'oz': 1
                }
            };

            if (fromUnit === toUnit || (fromUnit === 'dozen' && toUnit === 'dozen') || (fromUnit === 'piece' && toUnit ===
                    'piece')) {
                return quantity;
            } else if (conversionRates[fromUnit] && conversionRates[fromUnit][toUnit]) {
                return quantity * conversionRates[fromUnit][toUnit];
            } else {
                return quantity;
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const searchBar = document.getElementById('Search');
            const stockItems = document.querySelectorAll('.stockContainer p');

            searchBar.addEventListener('input', function() {
                const searchText = searchBar.value.toLowerCase();
                stockItems.forEach(item => {
                    const itemName = item.textContent.toLowerCase();
                    if (itemName.includes(searchText)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    </script>
@endsection
