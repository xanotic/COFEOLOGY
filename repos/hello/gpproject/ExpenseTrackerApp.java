import java.util.Scanner;

public class ExpenseTrackerApp {
    static Scanner scanner = new Scanner(System.in);

    public static void clearScreen() {
        System.out.print("\033[H\033[2J");
        System.out.flush();
    }

    public static void displayColoredMessage(String message, String colorCode) {
        System.out.print(colorCode); // Apply color
        System.out.println(message);
        System.out.print("\033[0m"); // Reset color
    }
    

    public static void displayMenu() {
        clearScreen();
        System.out.println("Expense Tracker Menu:");
        System.out.println("-----------------------");
        System.out.println("1. Add Expense");
        System.out.println("2. Add Income");
        System.out.println("3. View Expense");
        System.out.println("4. View Income"); // New option to view income
        System.out.println("5. Exit");
        System.out.println("6. Show Overall"); 
        System.out.println("-----------------------");
        System.out.print("Enter your choice: ");
    }


    public static void main(String[] args) {
        String[] expenseDates = new String[100];
        String[] expenseCategories = new String[100];
        double[] expenseAmounts = new double[100];
        int expenseCounter = 0;

        String[] incomeDates = new String[100];
        String[] incomeCategories = new String[100];
        double[] incomeAmounts = new double[100];
        int incomeCounter = 0;

        System.out.println("Welcome to Expense Tracker!");
        System.out.print("\nPress Enter to continue...");
        scanner.nextLine();

        int choice;

        do {
            displayMenu();
            choice = scanner.nextInt();

            switch (choice) {
                case 1:
                    Tracker.addExpense(expenseDates, expenseCategories, expenseAmounts, expenseCounter);
                    expenseCounter++;
                    break;
                case 2:
                    Tracker.addIncome(incomeDates, incomeCategories, incomeAmounts, incomeCounter);
                    incomeCounter++;
                    break;
                case 3:
                    Tracker.viewExpense(expenseDates, expenseCategories, expenseAmounts, expenseCounter);
                    break;
                case 4:
                    Tracker.viewIncome(incomeDates, incomeCategories, incomeAmounts, incomeCounter);
                    break;
                case 5:
                    clearScreen();
                    System.out.println("Exiting the Expense Tracker. Goodbye!");
                    break;
                case 6:
                    OverallTracker.showOverall(expenseDates, expenseAmounts, expenseCounter,
                    incomeDates, incomeAmounts, incomeCounter);
                    break;
                default:
                    clearScreen();
                    displayColoredMessage("Invalid choice. Please try again.", "\033[0;31m");
                    System.out.println("\n\nPress Enter to continue...");
                    scanner.nextLine();
                    scanner.nextLine();
                    break;
            }
        } while (choice != 5);

        scanner.close();
    }

    public class OverallTracker {

        public static void showOverall(String[] expenseDates, double[] expenseAmounts, int expenseCounter,
                                       String[] incomeDates, double[] incomeAmounts, int incomeCounter) {
            ExpenseTrackerApp.clearScreen();
            System.out.println("Overall Expenses and Income");
            System.out.println("---------------------------");
    
            double totalExpense = calculateTotal(expenseAmounts, expenseCounter);
            double totalIncome = calculateTotal(incomeAmounts, incomeCounter);
    
            System.out.printf("Total Expenses: $%.2f\n", totalExpense);
            System.out.printf("Total Income: $%.2f\n", totalIncome);
            System.out.printf("Overall Total: $%.2f\n", totalIncome - totalExpense);
    
            System.out.println("\n\nPress Enter to continue...");
            ExpenseTrackerApp.scanner.nextLine();
            ExpenseTrackerApp.scanner.nextLine();
        }
    
        private static double calculateTotal(double[] amounts, int counter) {
            double total = 0.0;
            for (int i = 0; i < counter; i++) {
                total += amounts[i];
            }
            return total;
        }
    }
}
