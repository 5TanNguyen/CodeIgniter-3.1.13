<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8' />
    <!-- <script src='../../assets/index.global.js'></script> -->
    <!-- <script src="
https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js
"></script> -->
    <script src="<?= base_url('assets/js/index.global.js') ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            <?php
            // Chuyển đổi mảng PHP thành JSON
            $eventsJson = json_encode($calendar);
            ?>
            // Chuyển mảng events từ PHP sang JavaScript
            var eventsFromPHP = <?php echo $eventsJson; ?>;

            var calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                },
                initialDate: new Date(),
                navLinks: true, // can click day/week names to navigate views
                businessHours: true, // display business hours
                editable: true,
                selectable: true,
                events: eventsFromPHP
                // events: [{
                //         title: 'Business Lunch',
                //         start: '2023-01-03T13:00:00',
                //         constraint: 'businessHours'
                //     },
                //     {
                //         title: 'Meeting',
                //         start: '2023-01-13T11:00:00',
                //         constraint: 'availableForMeeting', // defined below
                //         color: '#257e4a'
                //     },
                //     {
                //         title: 'Conference',
                //         start: '2023-01-18',
                //         end: '2023-01-20'
                //     },
                //     {
                //         title: 'Party',
                //         start: '2023-01-29T20:00:00'
                //     },

                //     // areas where "Meeting" must be dropped
                //     {
                //         groupId: 'availableForMeeting',
                //         start: '2023-01-11T10:00:00',
                //         end: '2023-01-11T16:00:00',
                //         display: 'background'
                //     },
                //     {
                //         groupId: 'availableForMeeting',
                //         start: '2023-01-13T10:00:00',
                //         end: '2023-01-13T16:00:00',
                //         display: 'background'
                //     },

                //     // red areas where no events can be dropped
                //     {
                //         start: '2023-01-24',
                //         end: '2023-01-28',
                //         overlap: false,
                //         display: 'background',
                //         color: '#ff9f89'
                //     },
                //     {
                //         start: '2023-01-06',
                //         end: '2023-01-08',
                //         overlap: false,
                //         display: 'background',
                //         color: '#ff9f89'
                //     }
                // ]
            });

            calendar.render();
        });
    </script>
    <style>
        body {
            margin: 40px 10px;
            padding: 0;
            font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
            font-size: 14px;
        }

        #calendar {
            max-width: 1100px;
            margin: 0 auto;
            z-index: 0;
        }
    </style>

    <style>
        /* Style for the modal popup */
        .modal {
            display: none;
            position: fixed;
            z-index: 5;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            overflow: auto;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(-100px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-content {
            background: linear-gradient(135deg, #6dd5ed, #2193b0);
            margin: 5% auto;
            padding: 30px;
            border-radius: 12px;
            width: 50%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.4s ease-out;
            color: white;
            font-family: Arial, sans-serif;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #ddd;
        }

        .modal-header h2 {
            margin: 0;
        }

        .modal-close {
            cursor: pointer;
            font-size: 28px;
            font-weight: bold;
            color: white;
        }

        .modal-body input,
        .modal-body textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            outline: none;
            font-size: 16px;
        }

        .modal-body input:focus,
        .modal-body textarea:focus {
            background-color: #e0f7fa;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            padding-top: 20px;
        }

        .modal-footer button {
            padding: 12px 20px;
            margin-left: 10px;
            border: none;
            cursor: pointer;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            font-weight: bold;
        }

        .btn-save {
            background-color: #4CAF50;
            color: white;
        }

        .btn-cancel {
            background-color: #f44336;
            color: white;
        }

        .btn-save:hover,
        .btn-cancel:hover {
            opacity: 0.9;
        }

        /* Button to trigger modal */
        .open-modal-btn {
            padding: 12px 25px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 50px;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease-in-out;
        }

        .open-modal-btn:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        /* Error message styles */
        .error-message {
            color: #ffcccc;
            background-color: #ff5555;
            padding: 10px;
            margin: 10px 0;
            border-radius: 8px;
            display: none;
            text-align: center;
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        /* Add transition for fading out */
        .modal.fade-out {
            animation: fadeOut 0.4s forwards;
        }
    </style>
</head>

<body>
    <!-- Button to open the modal -->
    <button class="open-modal-btn"><i>Add Event</i></button>

    <!-- The Modal -->
    <div id="calendarModal" class="modal">
        <div class="modal-content">
            <?php echo form_open_multipart('todo/calendar_add', ['id' => 'calendarForm']); ?>
            <div class="modal-header">
                <h2>Add Calendar Event</h2>
                <span class="modal-close">&times;</span>
            </div>
            <div class="modal-body">
                <div class="error-message" id="errorMessage">Please fill out all fields.</div>

                <label for="title">Title</label>
                <input type="text" id="title" name="title" placeholder="Event Title" required>

                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Event Description" rows="3" required></textarea>

                <label for="start">Start Date and Time</label>
                <input type="datetime-local" id="start" name="start" required>

                <label for="end">End Date and Time</label>
                <input type="datetime-local" id="end" name="end" required>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" id="cancelBtn">Cancel</button>
                <!-- <button type="submit" class="btn-save" id="saveBtn">Save</button> -->
                <button type="submit" class="btn-save">Save</button>
            </div>
            <br>
            <!-- <button type="submit" style="width: 100%" class="btn btn-primary btn-block">Submit</button> -->
            <?php echo form_close(); ?>
        </div>
    </div>

    <div id='calendar'></div>


    <script>
        // Get modal and form elements
        var modal = document.getElementById("calendarModal");
        var openModalBtn = document.querySelector(".open-modal-btn");
        var closeModalSpan = document.querySelector(".modal-close");
        var cancelBtn = document.getElementById("cancelBtn");
        var saveBtn = document.getElementById("saveBtn");
        var form = document.getElementById("calendarForm");
        var errorMessage = document.getElementById("errorMessage");

        // Open modal when button is clicked
        openModalBtn.onclick = function() {
            modal.style.display = "block";
            modal.classList.remove('fade-out'); // Reset animation when opening
        }

        // Close modal when close button (x) is clicked
        closeModalSpan.onclick = function() {
            closeModalSmoothly();
        }

        // Close modal when cancel button is clicked with smooth fade-out effect
        cancelBtn.onclick = function(e) {
            e.preventDefault();
            closeModalSmoothly();
            errorMessage.style.display = "none"; // Hide error message if it's visible
        }

        // Close modal if user clicks outside of modal content
        window.onclick = function(event) {
            if (event.target == modal) {
                closeModalSmoothly();
                errorMessage.style.display = "none"; // Hide error message if it's visible
            }
        }

        // Handle form submit
        saveBtn.onclick = function(e) {
            e.preventDefault();

            // Get form values
            var title = document.getElementById("title").value;
            var description = document.getElementById("description").value;
            var start = document.getElementById("start").value;
            var end = document.getElementById("end").value;

            // Simple validation
            if (!title || !description || !start || !end) {
                errorMessage.style.display = "block";
                return;
            }

            // Hide error message if form is valid
            errorMessage.style.display = "none";

            // Example: Send form data to server (this is a placeholder)
            console.log("Form Data:");
            console.log("Title: " + title);
            console.log("Description: " + description);
            console.log("Start: " + start);
            console.log("End: " + end);

            // After submitting, close the modal
            closeModalSmoothly();

            // Optionally reset the form
            form.reset();
        }

        // Function to close modal with smooth fade-out effect
        function closeModalSmoothly() {
            modal.classList.add('fade-out');
            setTimeout(function() {
                modal.style.display = "none";
                modal.classList.remove('fade-out');
            }, 400); // Duration matches the fadeOut animation time
        }
    </script>


</body>

</html>