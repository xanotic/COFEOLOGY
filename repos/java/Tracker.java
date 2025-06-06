package gpproject;
import java.util.Scanner;

public class Tracker {

    static Scanner scanner = new Scanner(System.in);

    public static void addExpense(String[] dates, String[] categories, double[] amounts, int counter) {
        ExpenseTrackerApp.clearScreen();
        System.out.println("Add Expense");
        System.out.println("-----------------------");
        System.out.print("Enter date (YYYY-MM-DD): ");
        dates[counter] = scanner.next();
        scanner.nextLine();
        System.out.print("Enter category: ");
        categories[counter] = scanner.nextLine();
        System.out.print("Enter amount: $");
        amounts[counter] = scanner.nextDouble();
        System.out.println();
        

        ExpenseTrackerApp.displayColoredMessage("Expense added successfully!", "\033[0;32m");
        System.out.println("\n\nPress Enter to continue...");
        scanner.nextLine(); // Consume newline
        scanner.nextLine(); // Wait for Enter key
    }


    public static void addIncome(String[] dates, String[] categories, double[] amounts, int counter) {
        ExpenseTrackerApp.clearScreen();
        System.out.println("Add Income");
        System.out.println("-----------------------");
        System.out.print("Enter date (YYYY-MM-DD): ");
        dates[counter] = scanner.next();
        scanner.nextLine();
        System.out.print("Enter category: ");
        categories[counter] = scanner.nextLine();
        System.out.print("Enter amount: $");
        amounts[counter] = scanner.nextDouble();
        System.out.println();


        ExpenseTrackerApp.displayColoredMessage("Income added successfully!", "\033[0;32m");
        System.out.println("\n\nPress Enter to continue...");
        scanner.nextLine(); // Consume newline
        scanner.nextLine(); // Wait for Enter key
    }



    public static void viewExpenseIncome(String[] dates, String[] categories, double[] amounts, int counter) {
        ExpenseTrackerApp.clearScreen();
        if (counter == 0) {
            System.out.println("Expense List is empty.\n");
        } else {
            System.out.printf("%-20s%-20s%-20s\n", "Date", "Category", "Amount");
            System.out.println("---------------------------------------------------------------");

            for (int j = 0; j < counter; j++) {
                System.out.printf("%-20s%-20s-$%-20.2f\n", dates[j], categories[j], amounts[j]);
                System.out.println("---------------------------------------------------------------");
            }
        }

        
        System.out.println("\n\nPress Enter to continue...");
        scanner.nextLine();
        scanner.nextLine();
    }

}