package gprojectril;

public class Expense
{
    private String expDate;
    private String expCategory;
    private double expAmount;

    public Expense(String expDate, String expCategory, double expAmount)
    {
        this.expDate = expDate;
        this.expCategory = expCategory;
        this.expAmount = expAmount;
    }

    public String getExpDate()
    {
        return expDate;
    }

    public String getExpCategory()
    {
        return expCategory;
    }

    public double getExpAmount()
    {
        return expAmount;
    }


}