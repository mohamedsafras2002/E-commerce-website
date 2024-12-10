const spinner = document.getElementById('loading-spinner');

export function showSpinner() {
    spinner.style.display = 'flex';
}

export function hideSpinner() {
    spinner.style.display = 'none';
}


