function showReplyForm(messageId, messageContent, userName) {
    document.getElementById("popupOverlay").style.display = "flex";
    document.getElementById("replyMessageId").value = messageId;
    document.getElementById("userName").textContent = userName;
    document.getElementById("userMessage").textContent = messageContent;
}

function hideReplyForm() {
    document.getElementById("popupOverlay").style.display = "none";
    document.getElementById("replyTb").value = '';
}

document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const replyStatus = urlParams.get("reply_status");

    if (replyStatus === "success") {
        alert("Reply sent successfully!");
        urlParams.delete("reply_status");
        window.history.replaceState(null, "", window.location.pathname);
        window.location.reload(); 
    } else if (replyStatus === "error") {
        alert("Failed to send the reply. Please try again.");
        urlParams.delete("reply_status");
        window.history.replaceState(null, "", window.location.pathname);
    }
});
