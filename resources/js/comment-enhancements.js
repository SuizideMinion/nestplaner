import EasyMDE from "easymde";
import { createPicker } from "picmo";
// import "picmo/styles/default-theme.css"; // ✅ korrekter Pfad für v5.8.5

const textarea = document.getElementById("commentContent");

if (textarea) {
    const easyMDE = new EasyMDE({
        element: textarea,
        spellChecker: false,
        autosave: false,
        placeholder: "Schreibe einen Kommentar...",
        toolbar: ["bold", "italic", "quote", "|", "unordered-list", "ordered-list", "|", "link", "preview"],
        status: false,
    });

    const emojiBtn = document.getElementById("emojiBtn");
    const pickerContainer = document.createElement("div");
    pickerContainer.id = "emojiPickerContainer";
    pickerContainer.style.position = "absolute";
    pickerContainer.style.bottom = "60px";
    pickerContainer.style.right = "10px";
    pickerContainer.style.zIndex = "1000";
    pickerContainer.style.display = "none";
    document.body.appendChild(pickerContainer);

    const picker = createPicker({
        rootElement: pickerContainer,
        showPreview: false,
        showSearch: false,
        theme: "light"
    });

    emojiBtn.addEventListener("click", () => {
        pickerContainer.style.display =
            pickerContainer.style.display === "none" ? "block" : "none";
    });

    picker.addEventListener("emoji:select", (event) => {
        const cm = easyMDE.codemirror;
        const doc = cm.getDoc();
        const cursor = doc.getCursor();
        doc.replaceRange(event.emoji, cursor);
        pickerContainer.style.display = "none";
    });
}
