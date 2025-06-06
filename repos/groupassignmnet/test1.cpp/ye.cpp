#include <iostream>
#include <vector>
#include <string>
#include <iomanip>
#include <cstdlib>
#include <algorithm>

using namespace std;

// Function to clear the console screen (cross-platform)
void clearScreen() {
#ifdef _WIN32
    system("cls");
#else
    system("clear");
#endif
}

// Function to display a colored message
void displayColoredMessage(const string& message, int colorCode) {
    cout << "\033[" << colorCode << "m" << message << "\033[0m"; // ANSI escape codes for colors
}

// Function to display the menu
void displayMenu() {
    clearScreen();
    cout << "Expense Tracker Menu:" << endl;
    cout << "-----------------------" << endl;
    cout << "1. Add Expense" << endl;
    cout << "2. View Expenses" << endl;
    cout << "3. Exit" << endl;
    cout << "-----------------------" << endl;
    cout << "Enter your choice: ";
}

// Function to add an expense to the tracker
void addExpense(vector<string>& dates, vector<string>& categories, vector<double>& amounts) {
    clearScreen();
    string date, category;
    double amount;
    
    cout << "Add Expense" << endl;
    cout << "-----------------------" << endl;
    cout << "Enter date (YYYY-MM-DD): ";
    cin >> date;
    cout << "Enter category: ";
    cin.ignore();
    getline(cin, category);
    cout << "Enter amount: $";
    cin >> amount;
    
    dates.push_back(date);
    categories.push_back(category);
    amounts.push_back(amount);
    
    displayColoredMessage("Expense added successfully!", 32); // Green text for success
    cout << endl;
}

// Function to view expenses and sort by date, also display the total amount
void viewExpenses(const vector<string>& dates, const vector<string>& categories, const vector<double>& amounts) {
    clearScreen();
    if (dates.empty()) {
        cout << "Expense List is Empty." << endl;
    } else {
        vector<pair<string, pair<string, double>>> sortedExpenses;

        for (size_t i = 0; i < dates.size(); i++) {
            sortedExpenses.push_back(make_pair(dates[i], make_pair(categories[i], amounts[i])));
        }

        // Sort expenses by date in ascending order
        sort(sortedExpenses.begin(), sortedExpenses.end());

        cout << "Expense List (Sorted by Date):" << endl;
        cout << "---------------------------------------------------------------" << endl;
        cout << setw(12) << "Date" << setw(20) << "Category" << setw(20) << "Amount" << endl;
        cout << "---------------------------------------------------------------" << endl;

        double totalAmount = 0.0;

        for (size_t i = 0; i < sortedExpenses.size(); i++) {
            cout << setw(12) << sortedExpenses[i].first << setw(20) << sortedExpenses[i].second.first << setw(15) << fixed << setprecision(2) << "$" << sortedExpenses[i].second.second << endl;
            totalAmount += sortedExpenses[i].second.second;
        }

        cout << "---------------------------------------------------------------" << endl;
        cout << setw(32) << "Total Amount:" << setw(15) << fixed << setprecision(2) << "$" << totalAmount << endl;
    }

    cout << "Press Enter to continue...";
    cin.ignore();
    cin.get();
}


int main() {
    vector<string> dates;
    vector<string> categories;
    vector<double> amounts;
    int choice;

    cout << "Welcome to Expense Tracker!" << endl;

    do {
        displayMenu();
        cin >> choice;

        switch (choice) {
            case 1:
                addExpense(dates, categories, amounts);
                break;
            case 2:
                viewExpenses(dates, categories, amounts);
                break;
            case 3:
                clearScreen();
                cout << "Exiting the Expense Tracker. Goodbye!" << endl;
                break;
            default:
                clearScreen();
                displayColoredMessage("Invalid choice. Please try again.", 31); // Red text for error
                cout << endl;
                break;
        }
    } while (choice != 3);

    return 0;
}
