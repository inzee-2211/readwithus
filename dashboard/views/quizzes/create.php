<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<style>
  .quizCreate { padding: 10px 0 30px; }
  .quizCreate__head { margin-bottom: 18px; }
  .quizCreate__title { font-size: 1.35rem; font-weight: 700; margin: 0 0 6px; }
  .quizCreate__sub { color: #6b7280; margin: 0; }

  .quizCreate__grid{
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    margin-top: 16px;
  }
  @media (max-width: 860px){
    .quizCreate__grid{ grid-template-columns: 1fr; }
  }

  .quizCreateCard{
    background: #fff;
    border: 1px solid rgba(17,24,39,.08);
    border-radius: 14px;
    padding: 18px;
    box-shadow: 0 8px 24px rgba(17,24,39,.06);
    transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
    height: 100%;
  }
  .quizCreateCard:hover{
    transform: translateY(-2px);
    box-shadow: 0 14px 34px rgba(17,24,39,.10);
    border-color: rgba(17,24,39,.14);
  }
  .quizCreateCard__badge{
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: .8rem;
    font-weight: 700;
    color: #111827;
    background: rgba(59,130,246,.10);
    border: 1px solid rgba(59,130,246,.18);
    padding: 6px 10px;
    border-radius: 999px;
    margin-bottom: 10px;
  }
  .quizCreateCard__h{
    font-size: 1.05rem;
    font-weight: 800;
    margin: 0 0 8px;
  }
  .quizCreateCard__p{
    color: #6b7280;
    margin: 0 0 14px;
    line-height: 1.45;
  }

  .quizCreateCard__actions{
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
  }
  .quizCreateCard__meta{
    font-size: .82rem;
    color: #6b7280;
    margin-top: 10px;
  }
</style>

<div class="container container--fixed quizCreate">
  <div class="page__head quizCreate__head">
    <h1 class="quizCreate__title"><?php echo Label::getLabel('LBL_ADD_NEW_EXAM'); ?></h1>
    <p class="quizCreate__sub">Choose how you want to create your exam.</p>
  </div>

  <div class="page__body">
    <div class="quizCreate__grid">

      <div class="quizCreateCard">
        <div class="quizCreateCard__badge">📝 Manual</div>
        <h4 class="quizCreateCard__h">Manual Creation</h4>
        <p class="quizCreateCard__p">Enter exam title/description and select questions one-by-one.</p>

        <div class="quizCreateCard__actions">
          <a class="btn btn--primary" href="<?php echo MyUtility::makeUrl('Quizzes', 'form'); ?>">
            Continue (Manual)
          </a>
          <a class="btn btn--bordered" href="<?php echo MyUtility::makeUrl('Quizzes'); ?>">
            Back
          </a>
        </div>

        <div class="quizCreateCard__meta">Best when you want full control over exam text and question selection.</div>
      </div>

      <div class="quizCreateCard">
        <div class="quizCreateCard__badge">📄 CSV</div>
        <h4 class="quizCreateCard__h">Bulk Upload (CSV)</h4>
        <p class="quizCreateCard__p">
          Upload a CSV. The CSV filename becomes the exam name. Description will be saved as <b>NULL</b>.
          Questions will be imported and attached automatically.
        </p>

        <div class="quizCreateCard__actions">
          <a class="btn btn--primary" href="<?php echo MyUtility::makeUrl('Quizzes', 'bulkForm'); ?>">
            Continue (CSV Upload)
          </a>
          <a class="btn btn--bordered" href="<?php echo MyUtility::makeUrl('Questions'); ?>" target="_blank">
            View Question Bank
          </a>
        </div>

        <div class="quizCreateCard__meta">Best for fast exam creation using spreadsheet/CSV export.</div>
      </div>

    </div>
  </div>
</div>