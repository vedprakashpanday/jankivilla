$(document).on("click", "#submit", function () {

    var name = $("#username").val().trim(); // ✅ Trim से Extra Spaces हटाएँ
    var pswd = $("#password").val().trim();

    // ✅ Step 1: Empty Fields Check करो
    if (name === "" || pswd === "") {
        $("#Husername").css("display", name === "" ? "block" : "none");
        $("#Hpassword").css("display", pswd === "" ? "block" : "none");
        return; // ✅ Stop Execution अगर कोई field खाली है
    }

    // ✅ Step 2: AJAX Request भेजो
    $.ajax({
        url: "adminloginfunction.php",
        type: "POST",
        data: {
            name: name,
            password: pswd
        },
        dataType: "json", // ✅ Correct Syntax
        success: function (data) {
            if (data.success == 1) {
             window.location.href = "UI/admin/dashboard.php";
                console.log("Login Successful!");
            } else {
                alert("❌ Invalid Credentials!");
                console.log(data.your_password);
                console.log(data.database_password);
                
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", error);
            alert("❌ Server Error! Try Again.");
        }
    });

});
