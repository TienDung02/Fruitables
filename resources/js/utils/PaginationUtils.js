export class PaginationUtils {
    // Get visible page numbers
    static getVisiblePages(currentPage, lastPage) {
        const pages = [];
        let start = Math.max(1, currentPage - 2);
        let end = Math.min(lastPage, currentPage + 2);

        // Adjust if we're near the beginning
        if (currentPage <= 3) {
            end = Math.min(lastPage, 5);
        }

        // Adjust if we're near the end
        if (currentPage >= lastPage - 2) {
            start = Math.max(1, lastPage - 4);
        }

        // Generate page numbers
        for (let i = start; i <= end; i++) {
            pages.push(i);
        }

        return pages;
    }
}
