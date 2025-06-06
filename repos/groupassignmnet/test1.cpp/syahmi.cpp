#include <iostream>
#include <iomanip>
#include <string>
#include <vector>

using namespace std;

void clearScreen()
{
    #ifdef _WIN32
    system("cls");
    #else
        system("clear");
    #endif
}

void displayColoredMessage(const string &message, int colorCode)
{
    cout << "\033[" << colorCode << "m" << message << "\033[0m";
}

void displayMenu()
{
    clearScreen();
    cout << "Expense Tracker Menu:" << endl;
    cout << "-----------------------" << endl;
    cout << "1. Add Expense" << endl;
    cout << "2. View Expense" << endl;
    cout << "3. Exit" << endl;
    cout << "-----------------------" << endl;
    cout << "Enter your choice: ";
}

void addExpense(vector<string> &dates, vector<string> &categories, vector<double> &amounts, int &counter)
{
    clearScreen();
    string tempDate, tempCategory;
    double tempAmount;

    for(int i = 0; i < 1; i++)
    {
        cout << "Add Expense" << endl;
        cout << "-----------------------" << endl;
        cout << "Enter date (YYYY-MM-DD): ";
        cin >> tempDate;
        cout << "Enter category: ";
        cin.ignore();
        getline(cin, tempCategory);
        cout << "Enter amount: $";
        cin >> tempAmount;
        cout << endl;

        dates.push_back(tempDate);
        categories.push_back(tempCategory);
        amounts.push_back(tempAmount);
        counter++;
    }

    displayColoredMessage("Expense added successfully!", 32);
    cout << endl << endl;

    cout << "Press Enter to continue...";
    cin.ignore();
    cin.get();
}



void viewExpenses(const vector<string> &dates, const vector<string> &categories, vector<double> &amounts, int counter, double totalAmount)
{
    clearScreen();
    if (counter == 0)
    {
        cout << "Expense List is empty." << endl << endl;
    }
    else
    {
        cout << setw(32) << "EXPENSE LIST" << endl;
        cout << "---------------------------------------------------------------" << endl;
        cout << setw(12) << "Date" << setw(20) << "Category" << setw(20) << "Amount" << endl;
        cout << "---------------------------------------------------------------" << endl;

        for (int j = 0; j < counter; j++)
        {
            cout << setw(12) << dates[j] << setw(20) << categories[j] << setw(15) << "$" << fixed << setprecision(2) << amounts[j] << endl;
            cout << "---------------------------------------------------------------" << endl;
            totalAmount += amounts[j];
        }
        cout << setw(32) << "Total Amount:" << setw(15) << fixed << setprecision(2) << "$" << totalAmount << endl << endl;      
    }
    cout << "Press Enter to continue...";
    cin.ignore();
    cin.get();
}






int main()
{
    vector<string> dates;
    vector<string> categories;
    vector<double> amounts;
    int choice, counter = 0;
    double totalAmount = 0;

    cout << "Welcome to Expense Tracker!" << endl;
    cout << endl << "Press Enter twice to continue..." << endl;
    cin.ignore();
    cin.get();   

    do
    {
        displayMenu();
        cin >> choice;

        switch (choice)
        {
            case 1:
                addExpense(dates, categories, amounts, counter);
                break;
            case 2:
                viewExpenses(dates, categories, amounts, counter, totalAmount);
                break;
            case 3:
                clearScreen();
                cout << "Exiting the Expense Tracker. Goodbye!" << endl;
                break;
            default:
                clearScreen();
                displayColoredMessage("Invalid choice. Please try again.", 31);
                cout << endl << endl;

                cout << "Press Enter to continue...";
                cin.ignore();
                cin.get();
                break;
        }   
    } while (choice != 3);
 return 0;
}
