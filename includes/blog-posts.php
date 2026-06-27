<?php
/**
 * Marketing blog posts — shared by blog listing and individual post pages.
 */

function get_blog_posts(): array
{
    return [
        'three-as-of-saving' => [
            'slug' => 'three-as-of-saving',
            'title' => 'The 3 A\'s of successful saving',
            'excerpt' => 'Remember the 3 A\'s for retirement saving: amount, account, and asset mix.',
            'category' => 'Retirement',
            'read_time' => '7 min',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDdU0VhcUZSt2EswSpcFyunQ-4QcFa5WqvO4lFUQC6-A1AISBDPK7pFwbcg50qMzSEVazevgbPq6ht_WYoWeclP6RJpPLK79lNJAj0iDLbePwarp7lCk4HvB7MakzlXtTQXQXNUjLRVWDOX_Fq8FppQNpBP0psh4M7x5o1WsO9t7TjlA2nHejkHZ9N2GirFuIz6czhLRjivRhHoKFl1HPiIIjvQNjdbuLHmOA8_3EeczKe-7Q797N122g',
            'points' => [
                ['title' => 'Amount — how much to save', 'body' => 'Aim to save a consistent percentage of income each year. Many planners suggest 10–15% of gross pay for retirement, including employer contributions when available.'],
                ['title' => 'Start early, even small', 'body' => 'Time in the market matters. Starting with a modest monthly contribution in your 20s or 30s can compound significantly over decades.'],
                ['title' => 'Increase savings over time', 'body' => 'Raise your contribution rate after pay raises, bonuses, or when major expenses end. Small annual increases add up without feeling drastic.'],
                ['title' => 'Account — pick the right vehicle', 'body' => 'Tax-advantaged accounts like IRAs and employer plans can reduce your tax burden and help savings grow more efficiently.'],
                ['title' => 'Roth vs. traditional', 'body' => 'Roth accounts use after-tax dollars but may offer tax-free growth. Traditional accounts may reduce taxable income today with taxes due in retirement.'],
                ['title' => 'Employer match', 'body' => 'If your employer offers a match, contributing enough to capture the full match is often the highest-return step you can take.'],
                ['title' => 'Asset mix — balance growth and stability', 'body' => 'Your mix of stocks, bonds, and cash should reflect your time horizon and comfort with market swings.'],
                ['title' => 'Age-based allocation', 'body' => 'Younger savers often hold more equities for growth. As retirement nears, many investors gradually shift toward more conservative holdings.'],
                ['title' => 'Rebalance periodically', 'body' => 'Market moves can drift your allocation away from your target. Review and rebalance at least once a year or after major life changes.'],
                ['title' => 'Review all three A\'s annually', 'body' => 'Each year, check whether your amount, account choices, and asset mix still fit your goals, income, and risk tolerance.'],
            ],
        ],
        'what-is-an-ira' => [
            'slug' => 'what-is-an-ira',
            'title' => 'What is an IRA?',
            'excerpt' => 'An individual retirement account (IRA) allows you to save money for retirement in a tax-advantaged way.',
            'category' => 'Retirement',
            'read_time' => '5 min',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBG7tznRMdI1buRD2cjZIXp6GB1KmOcd8OUgyeofQ7ozMRoLtvn2NJBy9xEr2ps3yYVgiY_A6SRvs8WYcRWbrGK7aPhTtl_uWkvdyy2OIfdcdtdZL9wKs3yyXfYLEDRE2yd04LyDt8rIiSzWhALKbc-UgYyEAsJuNTk2xXZPDXKuBKSQdWaZdWSx7aMke6Qng3wbCPN_uTJN_gGuNT5z5TUfaLYqNznfIGSPk0R_Qq0msP3FlqBMtN9pQ',
            'points' => [
                ['title' => 'A personal retirement account', 'body' => 'An IRA is an account you open in your own name to save and invest for retirement, separate from an employer-sponsored plan.'],
                ['title' => 'Tax advantages', 'body' => 'IRAs offer tax benefits that can help your savings grow faster than a standard taxable brokerage account.'],
                ['title' => 'Traditional IRA basics', 'body' => 'Contributions may be tax-deductible depending on income and workplace plan participation. Growth is tax-deferred until withdrawal.'],
                ['title' => 'Roth IRA basics', 'body' => 'Contributions are made with after-tax dollars. Qualified withdrawals in retirement may be tax-free, including earnings.'],
                ['title' => 'Annual contribution limits', 'body' => 'The IRS sets yearly limits on how much you can contribute. Limits apply across all IRAs you own, not per account.'],
                ['title' => 'Investment flexibility', 'body' => 'Inside an IRA you can typically hold stocks, ETFs, bonds, mutual funds, and other approved investments depending on your provider.'],
                ['title' => 'Withdrawal rules', 'body' => 'IRAs are designed for retirement. Taking money out before age 59½ may trigger taxes and penalties, with some exceptions.'],
                ['title' => 'Required minimum distributions', 'body' => 'Traditional IRAs generally require minimum withdrawals after a certain age. Roth IRAs have different distribution rules.'],
                ['title' => 'Rollovers from old 401(k)s', 'body' => 'If you change jobs, you may roll workplace savings into an IRA to consolidate accounts and simplify planning.'],
                ['title' => 'Who should consider an IRA', 'body' => 'Anyone with earned income who wants additional retirement savings beyond an employer plan—or who lacks a workplace plan—may benefit.'],
            ],
        ],
        'five-keys-retirement-income' => [
            'slug' => 'five-keys-retirement-income',
            'title' => '5 keys to a retirement income plan',
            'excerpt' => 'Understand the risks and know your needs for long-term security.',
            'category' => 'Retirement',
            'read_time' => '9 min',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCVmucBsWBy_GnwrPjBviQGdiLGncmVcmqr37ZzwYwYoQIZkMu2JVB7MfSIUSW2CoTBN7yTBLvP5Xwcr-zH2xOJE9o5uqdg9O_J0cjLpoC_yHyIPwFmwYZDuwjtuGBriO_b7IH1-YArgmMBw53WrjzZHZE7_eT9g797_UTLExAe7mj7HVOdd4sCKXJdgPA3WuXgmiEtpupHmu2I8TaXjWDfm571xO6tsv3Pd06kJN2qoUPr5pfk00AZ7A',
            'points' => [
                ['title' => 'Estimate your spending needs', 'body' => 'List essential expenses (housing, health care, food) and discretionary spending. A realistic budget is the foundation of any income plan.'],
                ['title' => 'Map income sources', 'body' => 'Identify expected income from Social Security, pensions, rental properties, dividends, and portfolio withdrawals.'],
                ['title' => 'Plan for inflation', 'body' => 'Living costs tend to rise over time. Your plan should assume purchasing power will erode unless income grows with inflation.'],
                ['title' => 'Account for health care', 'body' => 'Medical costs often increase in retirement. Include premiums, out-of-pocket expenses, and potential long-term care in your projections.'],
                ['title' => 'Understand withdrawal strategies', 'body' => 'Decide how much to withdraw each year. Common approaches balance portfolio longevity with lifestyle needs.'],
                ['title' => 'Manage sequence-of-returns risk', 'body' => 'Poor market returns early in retirement can deplete savings faster. Holding cash reserves or reducing withdrawals in downturns may help.'],
                ['title' => 'Diversify income streams', 'body' => 'Relying on a single source increases risk. A mix of guaranteed and market-based income can improve resilience.'],
                ['title' => 'Consider tax efficiency', 'body' => 'Withdraw from taxable, tax-deferred, and tax-free accounts in an order that minimizes lifetime taxes where possible.'],
                ['title' => 'Plan for longevity', 'body' => 'Retirement may last 20–30 years or more. Your plan should aim to support you even if you live longer than expected.'],
                ['title' => 'Review and adjust regularly', 'body' => 'Markets, tax laws, and personal circumstances change. Revisit your income plan at least annually with updated assumptions.'],
            ],
        ],
        'what-is-financial-planning' => [
            'slug' => 'what-is-financial-planning',
            'title' => 'What is financial planning?',
            'excerpt' => 'Learn how a financial plan could help you reach your goals through strategic capital allocation.',
            'category' => 'Wealth Management',
            'read_time' => '6 min',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBL6aZIZdLW1QHvXAEess1G5kIjBKCka0-BpFRm4r0BrHSAV5bB0dEPPULUmGILVmJUl_dHIgGjv78_qszucLvEQiSmm72RPYEMbuiyS7Qo4XB9GgrOg3XtwpSp6UXvXH4ObbrzMKE4SePJPCS1H-PiFLW4tPssaM7r97gAl4LaNPWvGy3hAWh5A0lyX4RSMRpdXUGjHN1OfbbeqS75D1wapQZP1khv38oSpYiqiODZ2LppySrwFrIdiw',
            'points' => [
                ['title' => 'A roadmap for your money', 'body' => 'Financial planning organizes your income, savings, investments, and goals into a coordinated strategy.'],
                ['title' => 'Clarifies priorities', 'body' => 'A plan helps you decide what matters most—retirement, education, real estate, or wealth transfer—and allocate resources accordingly.'],
                ['title' => 'Covers investing and protection', 'body' => 'Planning goes beyond picking stocks. It includes insurance, estate considerations, tax strategy, and cash-flow management.'],
                ['title' => 'Sets measurable targets', 'body' => 'Goals with timelines and dollar amounts are easier to track than vague intentions to "save more."'],
                ['title' => 'Balances risk and return', 'body' => 'Your plan should reflect how much market volatility you can tolerate without abandoning your strategy.'],
                ['title' => 'Adapts to life stages', 'body' => 'Marriage, children, career changes, and inheritance all require plan updates to stay relevant.'],
                ['title' => 'Integrates multiple accounts', 'body' => 'Brokerage, retirement, and real estate holdings work best when viewed as one household balance sheet.'],
                ['title' => 'Supports informed decisions', 'body' => 'Major purchases, career breaks, or early retirement become easier to evaluate against a written plan.'],
                ['title' => 'Professional guidance optional', 'body' => 'Many investors use advisors to stress-test assumptions, model scenarios, and stay accountable.'],
                ['title' => 'Living document', 'body' => 'A good financial plan is reviewed regularly—not filed away after one meeting.'],
            ],
        ],
        'three-strategies-building-wealth' => [
            'slug' => 'three-strategies-building-wealth',
            'title' => '3 effective strategies for building wealth',
            'excerpt' => 'Discover core principles that drive long-term asset accumulation and risk management.',
            'category' => 'Wealth Management',
            'read_time' => '5 min',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCBAww1P81qYKAvHS6oMvf0EK4bz-awnZAy0wLKhv2278WkzczeCQeigIV1IcUmdlLX7s7mq28Ga1W6fvExAG9UtrRxScJwGT8c0HNsk1m2_sisEXpOWm_RwxbB9CDnaEgPUE6Zh-rTi5QxOv8tQ9-TvkAeXt4ecYmS8sxEzVnlADqwKpq3Z3ZQEvqCqJfOYDCGoy2neHvtg7Mo-8_PPRdKyXxKwsAmvS094tlFP635xCHuoHB9Q-m5iw',
            'points' => [
                ['title' => 'Consistent investing', 'body' => 'Regular contributions to stocks, ETFs, or managed plans harness compounding and reduce the pressure to time the market.'],
                ['title' => 'Diversification', 'body' => 'Spreading investments across sectors, geographies, and asset classes can reduce the impact of any single downturn.'],
                ['title' => 'Long-term perspective', 'body' => 'Wealth is often built over decades. Short-term volatility is normal; reacting emotionally can undermine returns.'],
                ['title' => 'Tax-efficient growth', 'body' => 'Using retirement accounts and thoughtful asset location can leave more of your returns working for you.'],
                ['title' => 'Real estate as a complement', 'body' => 'Rental or REIT exposure can add income and diversification alongside traditional equities.'],
                ['title' => 'Control debt', 'body' => 'High-interest debt drains wealth faster than markets typically build it. Paying down costly debt is often a strong first move.'],
                ['title' => 'Emergency reserves', 'body' => 'Cash for unexpected expenses helps you avoid selling investments at the wrong time.'],
                ['title' => 'Reinvest dividends', 'body' => 'Reinvesting income accelerates compounding, especially in tax-advantaged accounts.'],
                ['title' => 'Avoid concentration risk', 'body' => 'Large bets on single stocks or sectors can produce outsized gains—but also severe losses.'],
                ['title' => 'Periodic review', 'body' => 'Annual check-ins keep your strategy aligned with goals as income and markets change.'],
            ],
        ],
        'power-of-planning-together' => [
            'slug' => 'power-of-planning-together',
            'title' => 'The power of planning together',
            'excerpt' => 'Why transparent financial communication within households is critical for generational wealth.',
            'category' => 'Wealth Management',
            'read_time' => '6 min',
            'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDfo2hnQ4UvfDZ-hQFhIWf0ewrjlcAElJLPrmtJc8f2eBBy42qnYLsldttedGlayn6qJ5ZGqbK_3x6-VpDHpSy8Xe-h1BhPepKOy7fVvJd2wTq-hr5wBNhnA6YKfneXMj2RCFk10tXsGZke_ylSdmmUcYUVGT7Y9XTS9cKN2DKbRvLvOsIv9SvF7Na1ut9AaTIhYbZJJ6zwXfr78O589Gam6637sacT2tkyMzikDUE-kkST7Ibu8jiEuw',
            'points' => [
                ['title' => 'Shared goals reduce conflict', 'body' => 'Couples and families who agree on priorities are less likely to make conflicting financial decisions.'],
                ['title' => 'Align on risk tolerance', 'body' => 'Discuss how much market fluctuation everyone can accept before investing aggressively.'],
                ['title' => 'Document major decisions', 'body' => 'Writing down goals, budgets, and investment policies creates clarity and accountability.'],
                ['title' => 'Include the next generation', 'body' => 'Teaching children about saving and investing early supports long-term family wealth.'],
                ['title' => 'Estate and beneficiary review', 'body' => 'Ensure accounts and insurance name the right beneficiaries and reflect current wishes.'],
                ['title' => 'Plan for major milestones', 'body' => 'Home purchases, education, and retirement each need coordinated saving and timing.'],
                ['title' => 'Regular family check-ins', 'body' => 'Quarterly or annual money meetings keep everyone informed without surprise crises.'],
                ['title' => 'Professional facilitation', 'body' => 'An advisor can mediate difficult conversations and present objective scenarios.'],
                ['title' => 'Transparency builds trust', 'body' => 'Hiding debt or accounts from partners often creates larger problems than the original issue.'],
                ['title' => 'Celebrate progress', 'body' => 'Acknowledging milestones—paying off debt, hitting savings targets—reinforces positive habits.'],
            ],
        ],
        'diversify-across-asset-classes' => [
            'slug' => 'diversify-across-asset-classes',
            'title' => 'Why diversification matters for long-term investors',
            'excerpt' => 'Spreading risk across stocks, bonds, and real estate can smooth the ride toward your goals.',
            'category' => 'Investing',
            'read_time' => '6 min',
            'image' => '/uploads/images/evergren_cmarket.png',
            'points' => [
                ['title' => 'Don\'t rely on one winner', 'body' => 'Even strong companies or sectors can underperform for years. Diversification limits dependence on any single bet.'],
                ['title' => 'Stocks for growth', 'body' => 'Equities have historically delivered higher long-term returns but with greater short-term volatility.'],
                ['title' => 'Bonds for stability', 'body' => 'Fixed income can cushion portfolios when stock markets decline, though returns vary with interest rates.'],
                ['title' => 'Real estate exposure', 'body' => 'Direct property or REITs can provide income and inflation-sensitive assets outside traditional equities.'],
                ['title' => 'Geographic spread', 'body' => 'International holdings may perform differently from domestic markets in the same year.'],
                ['title' => 'Sector balance', 'body' => 'Technology, health care, energy, and consumer sectors rarely move in lockstep.'],
                ['title' => 'Correlation shifts', 'body' => 'Assets that diversify in one period may move together in crises—review allocation after major events.'],
                ['title' => 'ETFs simplify diversification', 'body' => 'Broad index funds offer instant exposure to hundreds of companies at low cost.'],
                ['title' => 'Rebalance to stay on target', 'body' => 'Winners can become overweight. Selling high and buying low via rebalancing maintains your intended mix.'],
                ['title' => 'Diversification is not elimination of risk', 'body' => 'You can still lose money in a diversified portfolio—but extreme losses from one holding are less likely.'],
            ],
        ],
        'estate-planning-basics' => [
            'slug' => 'estate-planning-basics',
            'title' => 'Estate planning basics for growing portfolios',
            'excerpt' => 'Simple steps to protect what you build and pass wealth according to your wishes.',
            'category' => 'Wealth Management',
            'read_time' => '7 min',
            'image' => '/uploads/images/wallet_image3.png',
            'points' => [
                ['title' => 'Start with beneficiaries', 'body' => 'Retirement and brokerage accounts often pass by beneficiary designation—not your will. Keep them current.'],
                ['title' => 'Draft a will', 'body' => 'A will directs how assets flow and can name guardians for minor children.'],
                ['title' => 'Consider a trust', 'body' => 'Trusts may help manage complex assets, reduce probate delays, or control distributions over time.'],
                ['title' => 'Power of attorney', 'body' => 'Authorize someone you trust to handle finances if you become incapacitated.'],
                ['title' => 'Health care directives', 'body' => 'Document medical preferences and appoint someone to speak on your behalf.'],
                ['title' => 'Inventory your assets', 'body' => 'List accounts, real estate, insurance, and debts so heirs aren\'t left searching.'],
                ['title' => 'Tax implications', 'body' => 'Estate and inheritance rules vary. Planning ahead may reduce taxes on transfers.'],
                ['title' => 'Business interests', 'body' => 'If you own a business, succession planning protects value for family or partners.'],
                ['title' => 'Communicate intentions', 'body' => 'Surprises in estate plans often cause disputes. Discuss major decisions with heirs when appropriate.'],
                ['title' => 'Review every few years', 'body' => 'Marriage, divorce, births, and asset growth all warrant updates to estate documents.'],
            ],
        ],
        'managing-market-volatility' => [
            'slug' => 'managing-market-volatility',
            'title' => 'Managing market volatility as an investor',
            'excerpt' => 'Practical habits for staying invested when stock and real estate markets move unpredictably.',
            'category' => 'Investing',
            'read_time' => '8 min',
            'image' => '/uploads/images/nasa-Q1p7bh3SHj8-unsplash.jpg',
            'points' => [
                ['title' => 'Volatility is normal', 'body' => 'Stock markets routinely experience double-digit drawdowns. Expecting ups and downs reduces panic selling.'],
                ['title' => 'Match risk to timeline', 'body' => 'Money needed within a few years may belong in safer holdings than long-term retirement savings.'],
                ['title' => 'Avoid market timing', 'body' => 'Missing only a handful of the market\'s best days can significantly reduce long-term returns.'],
                ['title' => 'Dollar-cost averaging', 'body' => 'Investing fixed amounts on a schedule buys more shares when prices are low and fewer when high.'],
                ['title' => 'Maintain cash reserves', 'body' => 'Emergency funds prevent forced sales during downturns.'],
                ['title' => 'Tune out noise', 'body' => 'Headlines amplify fear and greed. Focus on your plan, not daily price moves.'],
                ['title' => 'Revisit—not react', 'body' => 'Scheduled reviews beat impulsive trades triggered by short-term swings.'],
                ['title' => 'Understand your holdings', 'body' => 'Knowing what you own and why makes it easier to hold through volatility.'],
                ['title' => 'Real estate cycles too', 'body' => 'Property values and REITs also fluctuate. Diversification across asset types helps.'],
                ['title' => 'Stay the course when fundamentals hold', 'body' => 'If goals and risk tolerance haven\'t changed, drastic portfolio shifts during selloffs often hurt more than help.'],
            ],
        ],
    ];
}

function get_blog_post(string $slug): ?array
{
    $posts = get_blog_posts();
    return $posts[$slug] ?? null;
}

function get_blog_posts_by_slugs(array $slugs): array
{
    $posts = get_blog_posts();
    $result = [];
    foreach ($slugs as $slug) {
        if (isset($posts[$slug])) {
            $result[] = $posts[$slug];
        }
    }
    return $result;
}

function render_blog_card(array $post): void
{
    $url = '/blog/' . rawurlencode($post['slug']);
    ?>
<article class="group bg-white rounded-xl overflow-hidden border border-surface-gray hover:shadow-md transition-shadow flex flex-col h-full">
<a href="<?php echo htmlspecialchars($url); ?>" class="block aspect-video overflow-hidden border-b border-surface-gray">
<img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="<?php echo htmlspecialchars($post['title']); ?>" src="<?php echo htmlspecialchars($post['image']); ?>">
</a>
<div class="p-md flex flex-col flex-grow">
<div class="flex items-center gap-xs text-on-surface-variant font-label-md text-label-md mb-xs">
<span class="uppercase tracking-widest text-[10px]"><?php echo htmlspecialchars($post['category']); ?></span>
<span class="w-1 h-1 bg-outline rounded-full"></span>
<span><?php echo htmlspecialchars($post['read_time']); ?></span>
</div>
<h3 class="font-headline-md text-headline-md text-on-surface group-hover:text-institutional-blue transition-colors mb-xs">
<a href="<?php echo htmlspecialchars($url); ?>"><?php echo htmlspecialchars($post['title']); ?></a>
</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-md flex-grow"><?php echo htmlspecialchars($post['excerpt']); ?></p>
<a class="text-institutional-blue font-label-md hover:underline" href="<?php echo htmlspecialchars($url); ?>">Read more</a>
</div>
</article>
    <?php
}
