const printButton = document.getElementById("print-button");

const printPage = () => {
	window.print();
};

printButton.addEventListener('click', () => {
	printPage();
});