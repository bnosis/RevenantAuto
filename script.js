document.addEventListener("DOMContentLoaded", function () {
    const hamburger = document.querySelector(".hamburger");
    const navLinks = document.querySelector(".nav-links");

    hamburger.addEventListener("click", function () {
        navLinks.classList.toggle("show");

        // Toggle active class for animation
        hamburger.classList.toggle("active");
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const addCarButton = document.getElementById("addCar");
    const form = document.getElementById("quoteForm");
    const totalPriceElement = document.getElementById("totalPrice");

    // Pricing table with indexed values
    const pricingTable = {
        "sedan": [150, 200, 150, 300, 500],
        "suv": [200, 250, 180, 400, 600]
    };

    const packageLabels = [
        "Interior",
        "Interior + Exterior",
        "Exterior + Wax",
        "Exterior + Polish + Wax",
        "Exterior + Paint Correction + Polish + Wax"
    ];

    function updateTotalPrice() {
        let total = 0;
        document.querySelectorAll(".car-entry").forEach(entry => {
            const carType = entry.querySelector(".car-type").value;
            const packageIndex = parseInt(entry.querySelector(".package-type").value);
            if (!isNaN(packageIndex)) {
                total += pricingTable[carType][packageIndex];
            }
        });
        totalPriceElement.textContent = `$${total}`;
    }

    function updatePackageOptions(entry) {
        const carType = entry.querySelector(".car-type").value;
        const packageSelect = entry.querySelector(".package-type");

        const newPackageSelect = packageSelect.cloneNode(false); // remove old children

        pricingTable[carType].forEach((price, index) => {
            const option = document.createElement("option");
            option.value = index; // submit index, not price
            option.textContent = `${packageLabels[index]} - $${price}`;
            newPackageSelect.appendChild(option);
        });

        newPackageSelect.addEventListener("change", updateTotalPrice);
        packageSelect.parentNode.replaceChild(newPackageSelect, packageSelect);

        updateTotalPrice();
    }

    function createCarEntry() {
        const carEntry = document.createElement("div");
        carEntry.classList.add("car-entry");

        carEntry.innerHTML = `
            <label for="carType">Car Type:</label>
            <select name="carType[]" class="car-type">
                <option value="sedan">Coupe or Sedan</option>
                <option value="suv">SUV, Van or Truck</option>
            </select>

            <label for="packageType">Package Type:</label>
            <select name="packageType[]" class="package-type"></select>

            <button type="button" class="remove-car">Remove</button>
        `;

        const carTypeSelect = carEntry.querySelector(".car-type");

        carTypeSelect.addEventListener("change", function () {
            updatePackageOptions(carEntry);
        });

        carEntry.querySelector(".remove-car").addEventListener("click", function () {
            carEntry.remove();
            updateTotalPrice();
        });

        updatePackageOptions(carEntry);
        return carEntry;
    }

    addCarButton.addEventListener("click", function () {
        const newCarEntry = createCarEntry();
        form.insertBefore(newCarEntry, addCarButton);
        updateTotalPrice(); 
    });

    // Init first entry
    const firstCarEntry = document.querySelector(".car-entry");
    if (firstCarEntry) {
        updatePackageOptions(firstCarEntry);
        firstCarEntry.querySelector(".car-type").addEventListener("change", function () {
            updatePackageOptions(firstCarEntry);
        });
    }

    updateTotalPrice();
});

document.addEventListener('DOMContentLoaded', function () {
    const phoneInput = document.getElementById('phone');

    phoneInput.addEventListener('input', function () {
        let input = phoneInput.value.replace(/\D/g, '');  // Remove all non-numeric characters

        // Limit input to 10 digits
        if (input.length > 10) {
            input = input.slice(0, 10);
        }

        let formattedInput = '';

        // Format the phone number as (xxx) xxx-xxxx
        if (input.length > 0) {
            formattedInput = `(${input.slice(0, 3)}`;
        }
        if (input.length > 3) {
            formattedInput += `) ${input.slice(3, 6)}`;
        }
        if (input.length > 6) {
            formattedInput += `-${input.slice(6, 10)}`;
        }

        phoneInput.value = formattedInput;
    });
});





