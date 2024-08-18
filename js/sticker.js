// Store the currently selected z-index and edit mode status
let selectedZIndex = 1;
let editMode = false; // Initially not in edit mode

// Function to set the z-index based on button click
function setZIndex(value) {
    if (editMode) {
        selectedZIndex = value;
        document.getElementById('zIndexInput').value = selectedZIndex;
        console.log('Selected Z-Index:', selectedZIndex);
    }
}

// Function to initialize dragging for a sticker
function initializeDrag(stickerElement) {
    stickerElement.addEventListener('mousedown', startDrag);
}

function startDrag(event) {
    if (!editMode) return; // Prevent dragging if not in edit mode

    const stickerElement = event.target;
    let offsetX = event.clientX - stickerElement.getBoundingClientRect().left;
    let offsetY = event.clientY - stickerElement.getBoundingClientRect().top;

    function drag(event) {
        stickerElement.style.left = event.clientX - offsetX + 'px';
        stickerElement.style.top = event.clientY - offsetY + 'px';
    }

    function stopDrag() {
        document.removeEventListener('mousemove', drag);
        document.removeEventListener('mouseup', stopDrag);

        // Save the position back to the database when dragging stops
        saveStickerPosition(stickerElement);
    }

    document.addEventListener('mousemove', drag);
    document.addEventListener('mouseup', stopDrag);
}

// Function to save sticker position
function saveStickerPosition(stickerElement) {
    const stickerId = stickerElement.dataset.stickerId;
    const posX = parseInt(stickerElement.style.left, 10);
    const posY = parseInt(stickerElement.style.top, 10);

    console.log("Sticker ID:", stickerId);
    console.log("Position X:", posX);
    console.log("Position Y:", posY);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'save_sticker_position.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(`id=${stickerId}&pos_x=${posX}&pos_y=${posY}`);

    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                console.log('Position saved:', xhr.responseText);
            } else {
                console.error('Failed to save position');
            }
        }
    };
}

// Toggle edit mode function
function toggleEditMode() {
    editMode = !editMode;
    console.log('Edit mode:', editMode);
}

// Function to upload a sticker with a specified z-index
function uploadSticker(zIndexValue) {
    const sticker = document.createElement('img');
    sticker.src = 'path/to/your/sticker.png'; // Replace with the actual path
    sticker.classList.add('sticker');
    sticker.style.position = 'absolute';
    sticker.style.zIndex = zIndexValue;
    sticker.style.left = '0px';
    sticker.style.top = '0px';

    document.body.appendChild(sticker);
    initializeDrag(sticker);
}

// Ensure the DOM is fully loaded before running the script
document.addEventListener("DOMContentLoaded", function() {
    // Event listeners for the upload buttons
    const uploadBelakangButton = document.getElementById('uploadBelakang');
    const uploadTengahButton = document.getElementById('uploadTengah');
    const uploadDepanButton = document.getElementById('uploadDepan');
    const saveStickersButton = document.getElementById('save-stickers');

    if (uploadBelakangButton) {
        uploadBelakangButton.addEventListener('click', function() {
            uploadSticker(1); // z-index: In front of the background, below everything else
        });
    }

    if (uploadTengahButton) {
        uploadTengahButton.addEventListener('click', function() {
            uploadSticker(2); // z-index: In front of the main profile section, but below the profile container
        });
    }

    if (uploadDepanButton) {
        uploadDepanButton.addEventListener('click', function() {
            uploadSticker(3); // z-index: Below everything else except sidebar1 and the mouse cursor
        });
    }

    if (saveStickersButton) {
        saveStickersButton.addEventListener('click', function() {
            let stickerData = [];

            document.querySelectorAll('.sticker').forEach(sticker => {
                stickerData.push({
                    id: sticker.getAttribute('data-sticker-id'),
                    x: parseInt(sticker.style.left, 10),
                    y: parseInt(sticker.style.top, 10),
                    width: parseInt(sticker.style.width, 10),
                    height: parseInt(sticker.style.height, 10),
                    z_index: parseInt(sticker.style.zIndex, 10)
                });
            });

            console.log(stickerData); // Check the data before sending it

            fetch('save_stickers.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(stickerData)
            })
            .then(response => response.text()) // Get the raw response text
            .then(data => {
                console.log('Raw response:', data); // Log it to see what was returned
                return JSON.parse(data); // Then try to parse it as JSON
            })
            .then(data => {
                console.log('Stickers saved successfully', data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }

    // Initialize drag on all existing stickers
    document.querySelectorAll('.sticker').forEach(initializeDrag);
});

// Ensure the sticker is draggable only in edit mode
document.querySelectorAll('.sticker').forEach(sticker => {
    sticker.onmousedown = function(event) {
        if (!editMode) return; // Only allow dragging in edit mode

        let shiftX = event.clientX - sticker.getBoundingClientRect().left;
        let shiftY = event.clientY - sticker.getBoundingClientRect().top;

        function moveAt(pageX, pageY) {
            sticker.style.left = pageX - shiftX + 'px';
            sticker.style.top = pageY - shiftY + 'px';

            console.log(`Sticker moved to: left=${sticker.style.left}, top=${sticker.style.top}`);
        }

        function onMouseMove(event) {
            moveAt(event.pageX, event.pageY);
        }

        document.addEventListener('mousemove', onMouseMove);

        sticker.onmouseup = function() {
            document.removeEventListener('mousemove', onMouseMove);
            sticker.onmouseup = null;
        };
    };

    sticker.ondragstart = function() {
        return false;
    };
});
