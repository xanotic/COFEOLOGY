public class Age2
{
    public int calcAge(String icNum)
    {
        int birthYear = Integer.parseInt(icNum.substring(0,2));
        int currentYear = 2024;
        int age;
        if (birthYear > 24)
            age = currentYear - (1900 + birthYear);
        else
            age = currentYear - (2000 + birthYear);
        return age;
    }
}