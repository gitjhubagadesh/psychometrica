<div class="row">
    <div class="col-md-6">
        <label for="rowsPerPage" class="me-2 mb-0">Rows per page:</label>
        <select id="rowsPerPage" class="form-select w-auto" ng-model="pagination.limit" ng-change="updateRowsPerPage()">
            <option ng-value="10">10</option>
            <option ng-value="25">25</option>
            <option ng-value="50">50</option>
            <option ng-value="100">100</option>
        </select>
    </div>
    <div class="col-md-6">
        <ul class="pagination pagination-sm m-t-none m-b-none text-end">
            <!-- First Page Button (Optional) -->
            <li ng-class="{ 'disabled': pagination.currentPage === 1 }">
                <a href ng-click="goToPage(1)"><i class="fa fa-angle-double-left"></i></a>
            </li>

            <!-- Previous Page Button -->
            <li ng-class="{ 'disabled': pagination.currentPage === 1 }">
                <a href ng-click="prevPage()"><i class="fa fa-chevron-left"></i></a>
            </li>

            <!-- Page Numbers (with ellipsis handling) -->
            <li ng-repeat="page in getPageNumbers() track by $index" 
                ng-class="{ 'active': pagination.currentPage === page, 'disabled': page === '...' }">
                <a href ng-if="page !== '...'" ng-click="goToPage(page)">{{ page}}</a>
                <span ng-if="page === '...'">...</span>
            </li>

            <!-- Next Page Button -->
            <li ng-class="{ 'disabled': pagination.currentPage === pagination.totalPages }">
                <a href ng-click="nextPage()"><i class="fa fa-chevron-right"></i></a>
            </li>

            <!-- Last Page Button (Optional) -->
            <li ng-class="{ 'disabled': pagination.currentPage === pagination.totalPages }">
                <a href ng-click="goToPage(pagination.totalPages)"><i class="fa fa-angle-double-right"></i></a>
            </li>
        </ul>
    </div>
</div>