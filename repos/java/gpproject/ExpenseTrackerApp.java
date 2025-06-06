package gpproject;

import java.util.Scanner;
import gpproject.Tracker;


public class ExpenseTrackerApp {
    static Scanner scanner = new Scanner(System.in);

    public static void clearScreen() {
        System.out.print("\033[H\033[2J");
        System.out.flush();
    }

    public static void displayColoredMessage(String message, String colorCode) {
        System.out.print(colorCode); // Apply color
        System.out.println(message);
        System.osut.print("\033[0m"); // Reset color
    }



    public static void displayMenu() {
        clearScreen();
        System.out.println("Expense Tracker Menu:");
        System.out.println("-----------------------");
        System.out.println("1. Add Expense");
        System.out.println("2. Add Income");
        System.out.println("3. View Expense");
        System.out.println("4. Exit");
        System.out.println("-----------------------");
        System.out.print("Enter your choice: ");
    }


    public static void main(String[] args) {
        String[] dates = new String[100];
        String[] categories = new String[100];
        double[] amounts = new double[100];
        int choice, counter = 0;

        System.out.println("Welcome to Expense Tracker!");
        System.out.print("\nPress Enter to continue...");
        scanner.nextLine();

        do {
            displayMenu();
            choice = scanner.nextInt();

            switch (choice) {
                case 1:
                    Tracker.addExpense(dates, categories, amounts, counter);
                    counter++;
                    break;
                case 2:
                    Tracker.addIncome(dates, categories, amounts, counter);
                    counter++;
                    break;
                case 3:
                    Tracker.viewExpenseIncome(dates, categories, amounts, counter);
                    break;
                case 4:
                    clearScreen();
                    System.out.println("Exiting the Expense Tracker. Goodbye!");
                    break;
                default:
                    clearScreen();
                    displayColoredMessage("Invalid choice. Please try again.", "\033[0;31m");
                    System.out.println("\n\nPress Enter to continue...");
                    scanner.nextLine();
                    scanner.nextLine();
                    break;
            }
        } while (choice != 4);

        scanner.close();
    }
}