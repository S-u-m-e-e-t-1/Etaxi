<?php
include_once __DIR__ . '/../models/Report.php';
include_once __DIR__ . '/../includes/database.php';  // Ensure the path is correct

class StatisticsController {
    private $stats;
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->stats = new Statistics($this->db);
    }

    public function getReportData() {
        // Get current year and month
        $currentYear = date('Y');
        $currentMonth = date('m');

        // Get filter values from request
        $year = isset($_GET['year']) ? $_GET['year'] : $currentYear;
        $month = isset($_GET['month']) ? $_GET['month'] : $currentMonth;

        // Get monthly statistics
        $monthlyStats = $this->stats->getMonthlyStats($year, $month);

        // Get yearly statistics for chart
        $yearlyStats = $this->stats->getYearlyStats($year);

        // Months array for display
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        return [
            'currentYear' => $currentYear,
            'currentMonth' => $currentMonth,
            'year' => $year,
            'month' => $month,
            'monthlyStats' => $monthlyStats,
            'yearlyStats' => $yearlyStats,
            'months' => $months,
            'generatedDate' => date('F j, Y')
        ];
    }

    public function showStats() {
        // Get total drivers
        $totalDrivers = $this->stats->getTotalDrivers();
        
        // Get total payments
        $totalPayments = $this->stats->getTotalPayments();
        
        // Get total kilometers
        $totalKilometers = $this->stats->getTotalKilometers();
        
        // Get total customers
        $totalCustomers = $this->stats->getTotalCustomers();

        return [
            'totalDrivers' => $totalDrivers,
            'totalPayments' => $totalPayments,
            'totalKilometers' => $totalKilometers,
            'totalCustomers' => $totalCustomers
        ];
    }
}
?>