#include <iostream>
#include <vector>
#include <string>
#include <iomanip>

using namespace std;

// Define a structure to represent an expense
struct Expense {
    string date;
    string category;
    double amount;
};

// Function to display the menu
void displayMenu() {
    cout << "Expense Tracker Menu:" << endl;
    cout << "1. Add Expense" << endl;
    cout << "2. View Expenses" << endl;
    cout << "3. Exit" << endl;
    cout << "Enter your choice: ";
}

// Function to add an expense to the tracker
void addExpense(vector<Expense>& expenses) {
    Expense expense;
    cout << "Enter date (YYYY-MM-DD): ";
    cin >> expense.date;
    cout << "Enter category: ";
    cin >> expense.category;
    cout << "Enter amount: $";
    cin >> expense.amount;
    expenses.push_back(expense);
    cout << "Expense added successfully!" << endl;
}

// Function to view expenses
void viewExpenses(const vector<Expense>& expenses) {
    if (expenses.empty()) {
        cout << "No expenses to display." << endl;
        return;
    }
    cout << "Expense List:" << endl;
    cout << setw(12) << "Date" << setw(15) << "Category" << setw(10) << "Amount" << endl;
    cout << setfill('-') << setw(37) << "-" << setfill(' ') << endl;
    for (const Expense& expense : expenses) {
        cout << setw(12) << expense.date << setw(15) << expense.category << "$" << setw(10) << fixed << setprecision(2) << expense.amount << endl;
    }
}

int main() {
    vector<Expense> expenses;
    int choice;

    cout << "Welcome to Expense Tracker!" << endl;

    do {
        displayMenu();
        cin >> choice;

        switch (choice) {
            case 1:
                addExpense(expenses);
                break;
            case 2:
                viewExpenses(expenses);
                break;
            case 3:
                cout << "Exiting the Expense Tracker. Goodbye!" << endl;
                break;
            default:
                cout << "Invalid choice. Please try again." << endl;
                break;
        }
    } while (choice != 3);

    return 0;
}
