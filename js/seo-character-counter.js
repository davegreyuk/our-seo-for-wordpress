document.addEventListener("DOMContentLoaded", function () {
    // Constants for SEO field limits
    const TITLE_MAX_LENGTH = 60;
    const DESCRIPTION_MAX_LENGTH = 160;

    // Get SEO fields
    const titleInput = document.getElementById("seo_wp_seo_title");
    const descriptionInput = document.getElementById("seo_wp_seo_description");

    // Create counter elements
    function createCounterElement(id, maxLength) {
        const counter = document.createElement("p");
        counter.id = id;
        counter.style.fontSize = "12px";
        counter.style.color = "#666";
        counter.style.marginTop = "5px";
        counter.textContent = `0 / ${maxLength} characters`;
        return counter;
    }

    // Append character counters
    if (titleInput) {
        const titleCounter = createCounterElement("seo-title-counter", TITLE_MAX_LENGTH);
        titleInput.parentNode.appendChild(titleCounter);
        updateCounter(titleInput, titleCounter, TITLE_MAX_LENGTH);
        titleInput.addEventListener("input", function () {
            updateCounter(titleInput, titleCounter, TITLE_MAX_LENGTH);
        });
    }

    if (descriptionInput) {
        const descriptionCounter = createCounterElement("seo-description-counter", DESCRIPTION_MAX_LENGTH);
        descriptionInput.parentNode.appendChild(descriptionCounter);
        updateCounter(descriptionInput, descriptionCounter, DESCRIPTION_MAX_LENGTH);
        descriptionInput.addEventListener("input", function () {
            updateCounter(descriptionInput, descriptionCounter, DESCRIPTION_MAX_LENGTH);
        });
    }

    // Update counter function
    function updateCounter(input, counter, maxLength) {
        const length = input.value.length;
        counter.textContent = `${length} / ${maxLength} characters`;

        if (length > maxLength) {
            counter.style.color = "red";
        } else if (length > maxLength - 10) {
            counter.style.color = "orange";
        } else {
            counter.style.color = "#666";
        }
    }
});
